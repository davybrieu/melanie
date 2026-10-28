<?php

namespace App\Support;

use App\Models\Photo;
use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

/**
 * Photos du site, ajoutées dans l'espace de gestion (/admin/photos).
 *
 * Le fichier d'origine est gardé dans la base de données (table photos_originaux, en base64) : il
 * survit aux déploiements, contrairement aux fichiers d'une release de Forge, part avec les
 * sauvegardes de la base et n'est jamais servi tel quel. Les pages reçoivent des variantes WebP
 * redimensionnées (/photos/{largeur}/{dossier}/{nom}-{empreinte}.webp), créées à l'ajout de la
 * photo puis, après un déploiement, à la première demande, et servies comme des fichiers
 * statiques. Le nom vient du texte alternatif (Google Images lit aussi le nom du fichier) ;
 * l'empreinte change à chaque nouveau fichier.
 *
 * Photos d'une séance : leur rôle dépend de leur position (la 1re est la photo principale de la
 * page séance…). Photos du dossier « site » : de leur emplacement (accueil, portrait, bon-cadeau).
 */
class Photos
{
    /** Dossiers exposés sur le site. */
    public const DOSSIERS = ['grossesse', 'nouveau-ne', 'famille', 'site'];

    /** Dossiers des séances : leurs photos forment une liste ordonnée. */
    public const SEANCES = ['grossesse', 'nouveau-ne', 'famille'];

    /** Emplacements nommés du dossier « site ». */
    public const EMPLACEMENTS = ['accueil', 'portrait', 'bon-cadeau'];

    /** Largeurs générées pour les attributs srcset. */
    public const LARGEURS = [480, 800, 1200, 1600, 2000];

    /** Plus grand côté minimal d'une photo ajoutée : en dessous, elle serait floue en grand. */
    public const COTE_MINIMUM = 1000;

    /** Au-delà, la photo décompressée occuperait trop de mémoire. */
    public const PIXELS_MAXIMUM = 60_000_000;

    private const QUALITE_WEBP = 82;

    /** @var array<string, list<Photo>> */
    private array $inventaire = [];

    /** @var Collection<string, Photo>|null */
    private ?Collection $emplacements = null;

    /**
     * Photos d'une séance, dans l'ordre choisi dans l'espace de gestion.
     *
     * @return list<array<string, mixed>>
     */
    public function dossier(string $dossier, ?int $limite = null, int $decalage = 0): array
    {
        return array_map(
            fn (Photo $photo) => $this->publique($photo),
            array_slice($this->lister($dossier), $decalage, $limite),
        );
    }

    /**
     * Photo n° $index d'une séance (0 = la photo principale).
     *
     * @return array<string, mixed>|null
     */
    public function une(string $dossier, int $index = 0): ?array
    {
        $photo = $this->lister($dossier)[$index] ?? null;

        return $photo ? $this->publique($photo) : null;
    }

    /**
     * Photo d'un emplacement du site (« portrait »…), avec repli facultatif sur une photo de séance.
     *
     * @return array<string, mixed>|null
     */
    public function nommee(string $emplacement, ?string $repli = null, int $index = 0): ?array
    {
        $this->emplacements ??= Photo::where('dossier', 'site')->whereNotNull('emplacement')->get()->keyBy('emplacement');
        $photo = $this->emplacements->get($emplacement);

        if ($photo) {
            return $this->publique($photo);
        }

        return $repli ? $this->une($repli, $index) : null;
    }

    /**
     * Crée (si besoin) la variante demandée et renvoie son chemin sur le disque.
     */
    public function variante(int $largeur, string $dossier, string $nom, string $empreinte): ?string
    {
        if (! in_array($largeur, self::LARGEURS, true)) {
            return null;
        }

        $photo = Photo::where('dossier', $dossier)->where('nom', $nom)->where('empreinte', $empreinte)->first();

        if (! $photo) {
            return null;
        }

        $destination = $this->cheminVariante($photo, $largeur);

        if (! is_file($destination)) {
            $this->creerVariantes($photo, [$largeur]);
        }

        return $destination;
    }

    /**
     * Données transmises aux pages.
     *
     * @return array{src: string, srcset: string, largeur: int, hauteur: int, alt: string}
     */
    public function publique(Photo $photo): array
    {
        $largeurs = $this->largeurs($photo);
        $url = fn (int $l) => "/photos/{$l}/{$photo->dossier}/{$photo->nom}-{$photo->empreinte}.webp";
        $defaut = collect($largeurs)->filter(fn (int $l) => $l <= 1200)->last() ?? $largeurs[0];

        return [
            'src' => $url($defaut),
            'srcset' => collect($largeurs)
                ->map(fn (int $l) => $url($l).' '.min($l, $photo->largeur).'w')
                ->implode(', '),
            'largeur' => $photo->largeur,
            'hauteur' => $photo->hauteur,
            'alt' => $photo->alt,
        ];
    }

    /**
     * Largeur et hauteur affichées d'un fichier image (rotation des téléphones comprise),
     * ou null s'il n'est pas lisible.
     *
     * @return array{int, int}|null
     */
    public function dimensions(string $fichier): ?array
    {
        $taille = @getimagesize($fichier);

        if (! $taille) {
            return null;
        }

        return $this->orientation($fichier) >= 5 ? [$taille[1], $taille[0]] : [$taille[0], $taille[1]];
    }

    /**
     * Ajoute une photo à la fin d'une séance, ou à un emplacement du site (elle remplace alors la
     * précédente). Ses variantes sont créées tout de suite : une photo illisible est refusée.
     *
     * @throws RuntimeException photo illisible
     */
    public function ajouter(UploadedFile $fichier, string $dossier, ?string $emplacement, string $alt): Photo
    {
        $chemin = $fichier->getRealPath();
        [$largeur, $hauteur] = $this->dimensions($chemin) ?? throw new RuntimeException('Photo illisible.');
        $contenu = file_get_contents($chemin);

        $photo = new Photo([
            'dossier' => $dossier,
            'emplacement' => $emplacement,
            'position' => 0,
            'nom' => $this->nom($alt, $dossier),
            'empreinte' => bin2hex(random_bytes(4)),
            'largeur' => $largeur,
            'hauteur' => $hauteur,
            'orientation' => $this->orientation($chemin),
            'alt' => $alt,
        ]);

        try {
            $this->creerVariantes($photo, $this->largeurs($photo), $contenu);
        } catch (Throwable $exception) {
            $this->supprimerVariantes($photo);

            throw new RuntimeException('Photo illisible.', previous: $exception);
        }

        $remplacee = DB::transaction(function () use ($photo, $contenu, $dossier, $emplacement) {
            $ancienne = $emplacement ? Photo::where('dossier', $dossier)->where('emplacement', $emplacement)->lockForUpdate()->first() : null;

            if ($ancienne) {
                $this->effacer($ancienne);
            } else {
                $photo->position = $emplacement ? 0 : (Photo::where('dossier', $dossier)->whereNull('emplacement')->max('position') ?? -1) + 1;
            }

            $photo->save();
            DB::table('photos_originaux')->insert(['photo_id' => $photo->id, 'contenu' => base64_encode($contenu)]);

            return $ancienne;
        });

        if ($remplacee) {
            $this->supprimerVariantes($remplacee);
        }

        return $photo;
    }

    /**
     * Nouveau texte alternatif ; le nom de l'adresse suit, et les variantes avec lui.
     */
    public function modifierAlt(Photo $photo, string $alt): void
    {
        $nom = $this->nom($alt, $photo->dossier);

        if ($nom !== $photo->nom) {
            $ancienne = clone $photo;
            $photo->nom = $nom;
            $this->creerVariantes($photo, $this->largeurs($photo));
            $this->supprimerVariantes($ancienne);
        }

        $photo->alt = $alt;
        $photo->save();
    }

    /**
     * Déplace une photo de séance : d'un rang vers le début (« avant ») ou vers la fin
     * (« apres »), ou au 1er rang (« premiere »), celui de la photo principale.
     */
    public function deplacer(Photo $photo, string $sens): void
    {
        DB::transaction(function () use ($photo, $sens) {
            $photos = Photo::seance($photo->dossier)->lockForUpdate()->get()->all();
            $index = collect($photos)->search(fn (Photo $autre) => $autre->is($photo));

            if ($index === false) {
                return;
            }

            $cible = match ($sens) {
                'avant' => max(0, $index - 1),
                'apres' => min(count($photos) - 1, $index + 1),
                default => 0,
            };

            array_splice($photos, $cible, 0, array_splice($photos, $index, 1));
            $this->numeroter($photos);
        });
    }

    /**
     * Supprime la photo, son fichier d'origine et ses variantes ; les suivantes remontent d'un rang.
     */
    public function supprimer(Photo $photo): void
    {
        DB::transaction(function () use ($photo) {
            $this->effacer($photo);

            if (! $photo->emplacement) {
                $this->numeroter(Photo::seance($photo->dossier)->lockForUpdate()->get()->all());
            }
        });

        $this->supprimerVariantes($photo);
    }

    /**
     * Nom de l'adresse publique, tiré du texte alternatif : « future-maman-au-bord-du-lac ».
     */
    public function nom(string $alt, string $dossier): string
    {
        $nom = Str::slug($alt);

        if (strlen($nom) > 60) {
            $nom = Str::beforeLast(substr($nom, 0, 61), '-');
        }

        return $nom !== '' ? $nom : "photo-{$dossier}";
    }

    /**
     * @return list<Photo>
     */
    private function lister(string $dossier): array
    {
        if (! in_array($dossier, self::SEANCES, true)) {
            return [];
        }

        return $this->inventaire[$dossier] ??= Photo::seance($dossier)->get()->all();
    }

    /**
     * @param  list<Photo>  $photos
     */
    private function numeroter(array $photos): void
    {
        foreach (array_values($photos) as $position => $photo) {
            if ($photo->position !== $position) {
                $photo->position = $position;
                $photo->save();
            }
        }
    }

    /**
     * Largeurs utiles : celles qui ne dépassent pas la photo (au moins la plus petite).
     *
     * @return list<int>
     */
    private function largeurs(Photo $photo): array
    {
        return array_values(array_filter(self::LARGEURS, fn (int $l) => $l <= $photo->largeur)) ?: [self::LARGEURS[0]];
    }

    private function cheminVariante(Photo $photo, int $largeur): string
    {
        return public_path("photos/{$largeur}/{$photo->dossier}/{$photo->nom}-{$photo->empreinte}.webp");
    }

    private function supprimerVariantes(Photo $photo): void
    {
        File::delete(array_map(fn (int $largeur) => $this->cheminVariante($photo, $largeur), self::LARGEURS));
    }

    /** La photo et son fichier d'origine, dans la base. */
    private function effacer(Photo $photo): void
    {
        DB::table('photos_originaux')->where('photo_id', $photo->id)->delete();
        $photo->delete();
    }

    private function original(Photo $photo): string
    {
        $contenu = DB::table('photos_originaux')->where('photo_id', $photo->id)->value('contenu');

        return $contenu !== null ? base64_decode($contenu) : throw new RuntimeException("Fichier d’origine introuvable : photo {$photo->id}");
    }

    /**
     * Variantes WebP, la photo d'origine n'étant décompressée qu'une fois. Aucune métadonnée
     * (position GPS, appareil…) ne passe dans les variantes.
     *
     * @param  list<int>  $largeurs
     * @param  string|null  $contenu  fichier d'origine, lu dans la base s'il n'est pas fourni
     */
    private function creerVariantes(Photo $photo, array $largeurs, ?string $contenu = null): void
    {
        // Une photo de 24 Mpx décompressée occupe ~100 Mo en mémoire.
        @ini_set('memory_limit', '512M');

        $source = $this->ouvrir($contenu ?? $this->original($photo), $photo->orientation);

        foreach ($largeurs as $largeur) {
            $l = min($largeur, imagesx($source));
            $h = (int) round(imagesy($source) * $l / imagesx($source));

            $variante = imagecreatetruecolor($l, $h);
            imagealphablending($variante, false);
            imagesavealpha($variante, true);
            imagecopyresampled($variante, $source, 0, 0, 0, 0, $l, $h, imagesx($source), imagesy($source));

            $destination = $this->cheminVariante($photo, $largeur);
            File::ensureDirectoryExists(dirname($destination));

            // Écriture atomique : un fichier à moitié écrit ne doit jamais être servi.
            $temporaire = $destination.'.'.Str::random(6).'.tmp';
            imagewebp($variante, $temporaire, self::QUALITE_WEBP);
            rename($temporaire, $destination);
        }
    }

    /**
     * Photo d'origine décompressée, tournée dans le bon sens (orientation EXIF des téléphones).
     */
    private function ouvrir(string $contenu, int $orientation): GdImage
    {
        $taille = @getimagesizefromstring($contenu);

        if (! $taille || ! in_array($taille[2], [IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_WEBP], true) || $taille[0] * $taille[1] > self::PIXELS_MAXIMUM) {
            throw new RuntimeException('Photo illisible.');
        }

        $source = @imagecreatefromstring($contenu);

        if (! $source) {
            throw new RuntimeException('Photo illisible.');
        }

        return match ($orientation) {
            2 => $this->retourner($source, IMG_FLIP_HORIZONTAL),
            3 => imagerotate($source, 180, 0),
            4 => $this->retourner($source, IMG_FLIP_VERTICAL),
            5 => $this->retourner(imagerotate($source, -90, 0), IMG_FLIP_HORIZONTAL),
            6 => imagerotate($source, -90, 0),
            7 => $this->retourner(imagerotate($source, 90, 0), IMG_FLIP_HORIZONTAL),
            8 => imagerotate($source, 90, 0),
            default => $source,
        };
    }

    private function orientation(string $fichier): int
    {
        if (! function_exists('exif_read_data') || (@getimagesize($fichier)[2] ?? null) !== IMAGETYPE_JPEG) {
            return 1;
        }

        return (int) (@exif_read_data($fichier)['Orientation'] ?? 1);
    }

    private function retourner(GdImage $image, int $mode): GdImage
    {
        imageflip($image, $mode);

        return $image;
    }
}
