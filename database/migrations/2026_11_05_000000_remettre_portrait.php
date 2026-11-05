<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Portrait de Mélanie (database/photos/portrait.jpg, la photo qu'elle a fournie, sans métadonnées) :
 * mis en place dans toute base qui n'a pas de portrait, une base neuve par exemple. Un portrait
 * ajouté dans l'espace de gestion (/admin/photos) n'est jamais remplacé.
 */
return new class extends Migration
{
    public function up(): void
    {
        $source = database_path('photos/portrait.jpg');
        $taille = is_file($source) ? getimagesize($source) : false;

        if (! $taille || DB::table('photos')->where('dossier', 'site')->where('emplacement', 'portrait')->exists()) {
            return;
        }

        $alt = 'Mélanie, photographe à Dijon';

        DB::transaction(function () use ($source, $taille, $alt) {
            $id = DB::table('photos')->insertGetId([
                'dossier' => 'site',
                'emplacement' => 'portrait',
                'position' => 0,
                'nom' => Str::slug($alt),
                'empreinte' => bin2hex(random_bytes(4)),
                'largeur' => $taille[0],
                'hauteur' => $taille[1],
                'orientation' => 1,
                'alt' => $alt,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('photos_originaux')->insert(['photo_id' => $id, 'contenu' => base64_encode(file_get_contents($source))]);
        });
    }

    public function down(): void
    {
        // Le portrait reste : il a pu être remplacé depuis dans l'espace de gestion.
    }
};
