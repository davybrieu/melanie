<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Photos du site, gérées dans l'espace de gestion (/admin/photos).
 *
 * Le fichier d'origine est gardé dans la base de données (photos_originaux, en base64) : il
 * survit aux déploiements, contrairement aux fichiers d'une release de Forge, et part avec les
 * sauvegardes de la base. Les variantes WebP en sont tirées à la demande.
 *
 * Reprend les photos déposées jusqu'ici dans resources/photos (seul le portrait existait), avec
 * le texte alternatif que le site en tirait. Ce dossier a été retiré ensuite : sans lui, rien à reprendre.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();
            // grossesse, nouveau-ne, famille, ou site pour les photos à emplacement nommé.
            $table->string('dossier', 20);
            // Photos du site : accueil, portrait ou bon-cadeau (une par emplacement).
            $table->string('emplacement', 20)->nullable();
            // Ordre dans le dossier : la 1re est la photo principale de la page séance, etc.
            $table->unsignedSmallInteger('position')->default(0);
            // Adresse des variantes : /photos/{largeur}/{dossier}/{nom}-{empreinte}.webp
            $table->string('nom', 80);
            $table->char('empreinte', 8);
            // Dimensions affichées, et rotation EXIF du fichier d'origine (1 : aucune).
            $table->unsignedSmallInteger('largeur');
            $table->unsignedSmallInteger('hauteur');
            $table->unsignedTinyInteger('orientation')->default(1);
            $table->string('alt', 125);
            $table->timestamps();

            $table->index(['dossier', 'position']);
            $table->unique(['dossier', 'emplacement']);
        });

        Schema::create('photos_originaux', function (Blueprint $table) {
            $table->foreignId('photo_id')->primary()->constrained('photos')->cascadeOnDelete();
            $table->longText('contenu');
        });

        $this->reprendre();
    }

    public function down(): void
    {
        Schema::dropIfExists('photos_originaux');
        Schema::dropIfExists('photos');
    }

    /** Photos de resources/photos/{dossier}, triées par nom de fichier, comme le site les lisait. */
    private function reprendre(): void
    {
        foreach (['grossesse', 'nouveau-ne', 'famille', 'site'] as $dossier) {
            $chemin = resource_path("photos/{$dossier}");

            if (! is_dir($chemin)) {
                continue;
            }

            $fichiers = array_filter(scandir($chemin), fn (string $fichier) => in_array(strtolower(pathinfo($fichier, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true));
            natcasesort($fichiers);
            $position = 0;

            foreach ($fichiers as $fichier) {
                $source = "{$chemin}/{$fichier}";
                $taille = @getimagesize($source);
                $base = pathinfo($fichier, PATHINFO_FILENAME);
                $emplacement = $dossier === 'site' ? Str::slug($base) : null;

                if (! $taille || ($dossier === 'site' && ! in_array($emplacement, ['accueil', 'portrait', 'bon-cadeau'], true))) {
                    continue;
                }

                $orientation = function_exists('exif_read_data') && $taille[2] === IMAGETYPE_JPEG ? (int) (@exif_read_data($source)['Orientation'] ?? 1) : 1;
                $alt = $this->alt($dossier, $base);

                $id = DB::table('photos')->insertGetId([
                    'dossier' => $dossier,
                    'emplacement' => $emplacement,
                    'position' => $emplacement ? 0 : $position++,
                    'nom' => Str::limit(Str::slug($alt), 60, '') ?: "photo-{$dossier}",
                    'empreinte' => bin2hex(random_bytes(4)),
                    'largeur' => $orientation >= 5 ? $taille[1] : $taille[0],
                    'hauteur' => $orientation >= 5 ? $taille[0] : $taille[1],
                    'orientation' => $orientation,
                    'alt' => $alt,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('photos_originaux')->insert(['photo_id' => $id, 'contenu' => base64_encode(file_get_contents($source))]);
            }
        }
    }

    /** Texte alternatif que le site tirait du nom de fichier (« 03-ventre-rond.jpg » : « Ventre rond »). */
    private function alt(string $dossier, string $base): string
    {
        $parDefaut = [
            'grossesse' => 'Séance photo grossesse à Dijon par Mélanie Photographie',
            'nouveau-ne' => 'Séance photo nouveau-né à Dijon par Mélanie Photographie',
            'famille' => 'Séance photo famille à Dijon par Mélanie Photographie',
            'site' => 'Mélanie, photographe à Dijon',
        ];

        if ($dossier === 'site') {
            return $parDefaut['site'];
        }

        $texte = trim(preg_replace('/^\d+\s*[-_.)]*\s*/', '', str_replace(['_', '-'], ' ', $base)));

        if ($texte === '' || preg_match('/^(img|dsc|dscf|dji|pxl|photo|_?mg|p)\s*\d+/i', $texte) || ! preg_match('/\p{L}{3,}/u', $texte)) {
            return $parDefaut[$dossier];
        }

        return Str::limit(Str::ucfirst(preg_replace('/\s+/', ' ', $texte)), 125, '');
    }
};
