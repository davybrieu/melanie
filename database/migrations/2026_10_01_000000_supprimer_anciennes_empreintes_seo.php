<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\File;

/**
 * Nettoyage unique, exécuté au déploiement : l'ancienne version de « seo:generer »
 * mémorisait l'empreinte des pages dans storage/app/private/seo/empreintes.json.
 * La commande actuelle ne s'en sert plus.
 */
return new class extends Migration
{
    public function up(): void
    {
        File::deleteDirectory(storage_path('app/private/seo'));
    }
};
