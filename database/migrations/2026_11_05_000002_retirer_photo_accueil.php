<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * L'accueil n'a plus de grande photo (choix de Mélanie, novembre 2026) : la photo de l'emplacement
 * « accueil », devenu inutile, est retirée avec son fichier d'origine. Ses variantes WebP, propres
 * à chaque release, ne sont plus recréées.
 */
return new class extends Migration
{
    public function up(): void
    {
        $ids = DB::table('photos')->where('dossier', 'site')->where('emplacement', 'accueil')->pluck('id');

        DB::table('photos_originaux')->whereIn('photo_id', $ids)->delete();
        DB::table('photos')->whereIn('id', $ids)->delete();
    }

    public function down(): void
    {
        // Rien à remettre : l'accueil n'affiche plus de grande photo.
    }
};
