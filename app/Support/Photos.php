<?php

namespace App\Support;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Photos du site, déposées dans resources/photos/{dossier}.
 *
 * Les pages reçoivent des URL de variantes WebP redimensionnées
 * (/photos/{largeur}/{dossier}/{nom}-{empreinte}.webp). Chaque variante est
 * générée à la première demande puis servie comme un fichier statique ;
 * l'empreinte change quand la photo source est remplacée.
 */
class Photos
{
    /** Dossiers de resources/photos exposés sur le site. */
    public const DOSSIERS = ['grossesse', 'nouveau-ne', 'famille', 'site'];

    /** Largeurs générées pour les attributs srcset. */
    public const LARGEURS = [480, 800, 1200, 1600, 2000];

    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp'];

    private const QUALITE_WEBP = 82;

    private const ALT_PAR_DEFAUT = [
        'grossesse' => 'Séance photo grossesse à Dijon par Mélanie Photographie',
        'nouveau-ne' => 'Séance photo nouveau-né à Dijon par Mélanie Photographie',
        'famille' => 'Séance photo famille à Dijon par Mélanie Photographie',
        'site' => 'Mélanie, photographe à Dijon',
    ];

    /** @var array<string, list<array<string, mixed>>> */
    private array $inventaire = [];

    /**
     * Photos d'un dossier, triées par nom de fichier (préfixez-les 01-, 02-… pour choisir l'ordre).
     *
     * @return list<array<string, mixed>>
     */
    public function dossier(string $dossier, ?int $limite = null, int $decalage = 0): array
    {
        return array_map(
            fn (array $photo) => $this->publique($photo),
            array_slice($this->lister($dossier), $decalage, $limite),
        );
    }

    /**
     * Photo n° $index d'un dossier (0 = la première).
     *
     * @return array<string, mixed>|null
     */
    public function une(string $dossier, int $index = 0): ?array
    {
        $photo = $this->lister($dossier)[$index] ?? null;

        return $photo ? $this->publique($photo) : null;
    }

    /**
     * Photo nommée du dossier « site » (ex. « portrait » pour portrait.jpg),
     * avec repli facultatif sur une photo d'une autre catégorie.
     *
     * @return array<string, mixed>|null
     */
    public function nommee(string $nom, ?string $repli = null, int $index = 0): ?array
    {
        foreach ($this->lister('site') as $photo) {
            if ($photo['nom'] === $nom) {
                return $this->publique($photo);
            }
        }

        return $repli ? $this->une($repli, $index) : null;
    }

    /**
     * Génère (si besoin) la variante demandée et renvoie son chemin sur le disque.
     */
    public function variante(int $largeur, string $dossier, string $nom, string $empreinte): ?string
    {
        if (! in_array($largeur, self::LARGEURS, true)) {
            return null;
        }

        $photo = collect($this->lister($dossier))
            ->first(fn (array $photo) => $photo['nom'] === $nom && $photo['empreinte'] === $empreinte);

        if (! $photo) {
            return null;
        }

        $destination = public_path("photos/{$largeur}/{$dossier}/{$nom}-{$empreinte}.webp");

        if (! is_file($destination)) {
            $this->redimensionner($photo, $largeur, $destination);
        }

        return $destination;
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function lister(string $dossier): array
    {
        if (! in_array($dossier, self::DOSSIERS, true)) {
            return [];
        }

        return $this->inventaire[$dossier] ??= $this->scanner($dossier);
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function scanner(string $dossier): array
    {
        $chemin = resource_path("photos/{$dossier}");

        if (! is_dir($chemin)) {
            return [];
        }

        $fichiers = array_filter(
            scandir($chemin),
            fn (string $fichier) => in_array(strtolower(pathinfo($fichier, PATHINFO_EXTENSION)), self::EXTENSIONS, true),
        );

        natcasesort($fichiers);

        $photos = [];

        foreach ($fichiers as $fichier) {
            $absolu = "{$chemin}/{$fichier}";
            $taille = @getimagesize($absolu);

            if (! $taille) {
                continue;
            }

            [$largeur, $hauteur] = $this->orientation($absolu) >= 5 ? [$taille[1], $taille[0]] : [$taille[0], $taille[1]];

            $photos[] = [
                'dossier' => $dossier,
                'fichier' => $absolu,
                'nom' => Str::slug(pathinfo($fichier, PATHINFO_FILENAME)),
                'empreinte' => substr(md5(filemtime($absolu).'-'.filesize($absolu)), 0, 8),
                'largeur' => $largeur,
                'hauteur' => $hauteur,
                'alt' => $this->alt($dossier, pathinfo($fichier, PATHINFO_FILENAME)),
            ];
        }

        return $photos;
    }

    /**
     * Données transmises aux pages.
     *
     * @param  array<string, mixed>  $photo
     * @return array<string, mixed>
     */
    private function publique(array $photo): array
    {
        $largeurs = array_values(array_filter(self::LARGEURS, fn (int $l) => $l <= $photo['largeur'])) ?: [self::LARGEURS[0]];
        $url = fn (int $l) => "/photos/{$l}/{$photo['dossier']}/{$photo['nom']}-{$photo['empreinte']}.webp";
        $defaut = collect($largeurs)->filter(fn (int $l) => $l <= 1200)->last() ?? $largeurs[0];

        return [
            'src' => $url($defaut),
            'srcset' => collect($largeurs)
                ->map(fn (int $l) => $url($l).' '.min($l, $photo['largeur']).'w')
                ->implode(', '),
            'largeur' => $photo['largeur'],
            'hauteur' => $photo['hauteur'],
            'alt' => $photo['alt'],
        ];
    }

    /**
     * Texte alternatif tiré du nom de fichier (« 03-ventre-rond-au-lac-kir.jpg »),
     * ou texte par défaut pour les noms d'appareil (« IMG_1234.jpg »).
     */
    private function alt(string $dossier, string $nomFichier): string
    {
        $texte = trim(preg_replace('/^\d+\s*[-_.)]*\s*/', '', str_replace(['_', '-'], ' ', $nomFichier)));

        if ($texte === '' || preg_match('/^(img|dsc|dscf|dji|pxl|photo|_?mg|p)\s*\d+/i', $texte) || ! preg_match('/\p{L}{3,}/u', $texte)) {
            return self::ALT_PAR_DEFAUT[$dossier];
        }

        return Str::ucfirst(preg_replace('/\s+/', ' ', $texte));
    }

    private function orientation(string $fichier): int
    {
        if (! function_exists('exif_read_data') || ! in_array(strtolower(pathinfo($fichier, PATHINFO_EXTENSION)), ['jpg', 'jpeg'], true)) {
            return 1;
        }

        return (int) (@exif_read_data($fichier)['Orientation'] ?? 1);
    }

    /**
     * @param  array<string, mixed>  $photo
     */
    private function redimensionner(array $photo, int $largeur, string $destination): void
    {
        // Une photo de 24 Mpx décompressée occupe ~100 Mo en mémoire.
        @ini_set('memory_limit', '512M');

        $source = match (strtolower(pathinfo($photo['fichier'], PATHINFO_EXTENSION))) {
            'png' => imagecreatefrompng($photo['fichier']),
            'webp' => imagecreatefromwebp($photo['fichier']),
            default => imagecreatefromjpeg($photo['fichier']),
        };

        if (! $source) {
            throw new RuntimeException("Photo illisible : {$photo['fichier']}");
        }

        $source = match ($this->orientation($photo['fichier'])) {
            2 => $this->retourner($source, IMG_FLIP_HORIZONTAL),
            3 => imagerotate($source, 180, 0),
            4 => $this->retourner($source, IMG_FLIP_VERTICAL),
            5 => $this->retourner(imagerotate($source, -90, 0), IMG_FLIP_HORIZONTAL),
            6 => imagerotate($source, -90, 0),
            7 => $this->retourner(imagerotate($source, 90, 0), IMG_FLIP_HORIZONTAL),
            8 => imagerotate($source, 90, 0),
            default => $source,
        };

        $l = min($largeur, imagesx($source));
        $h = (int) round(imagesy($source) * $l / imagesx($source));

        $variante = imagecreatetruecolor($l, $h);
        imagealphablending($variante, false);
        imagesavealpha($variante, true);
        imagecopyresampled($variante, $source, 0, 0, 0, 0, $l, $h, imagesx($source), imagesy($source));

        File::ensureDirectoryExists(dirname($destination));

        // Écriture atomique : un fichier à moitié écrit ne doit jamais être servi.
        $temporaire = $destination.'.'.Str::random(6).'.tmp';
        imagewebp($variante, $temporaire, self::QUALITE_WEBP);
        rename($temporaire, $destination);
    }

    private function retourner(\GdImage $image, int $mode): \GdImage
    {
        imageflip($image, $mode);

        return $image;
    }
}
