<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tous les avis avec un texte sont gardés ; l'espace de gestion choisit ceux qui sont affichés.
 * Les avis déjà enregistrés étaient tous affichés : ils le restent.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avis_google', function (Blueprint $table) {
            $table->boolean('affiche')->default(true);
        });
    }

    public function down(): void
    {
        Schema::table('avis_google', function (Blueprint $table) {
            $table->dropColumn('affiche');
        });
    }
};
