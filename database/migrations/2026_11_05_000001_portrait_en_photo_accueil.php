<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Photo d'accueil (grande photo du haut de l'accueil) : le portrait de Mélanie
 * (database/photos/portrait.jpg), à sa demande, dans toute base qui n'a pas de photo d'accueil.
 * Une photo d'accueil ajoutée dans l'espace de gestion (/admin/photos) n'est jamais remplacée.
 */
return new class extends Migration
{
    public function up(): void
    {
        $source = database_path('photos/portrait.jpg');
        $taille = is_file($source) ? getimagesize($source) : false;

        if (! $taille || DB::table('photos')->where('dossier', 'site')->where('emplacement', 'accueil')->exists()) {
            return;
        }

        // Différent de celui du portrait : chaque photo a son propre texte alternatif.
        $alt = 'Mélanie, photographe à Dijon, accoudée près de son appareil photo, devant un mur de tirages';

        // Nom des variantes, comme Photos::nom : 60 caractères au plus, coupé à un tiret.
        $nom = Str::slug($alt);

        if (strlen($nom) > 60) {
            $nom = Str::beforeLast(substr($nom, 0, 61), '-');
        }

        DB::transaction(function () use ($source, $taille, $alt, $nom) {
            $id = DB::table('photos')->insertGetId([
                'dossier' => 'site',
                'emplacement' => 'accueil',
                'position' => 0,
                'nom' => $nom,
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
        // La photo d'accueil reste : elle a pu être remplacée depuis dans l'espace de gestion.
    }
};
