<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Avis de la fiche Google, actualisés chaque jour par la commande avis:actualiser.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Note globale et nombre d'avis de la fiche : une seule ligne.
        Schema::create('note_google', function (Blueprint $table) {
            $table->id();
            $table->float('note')->nullable();
            $table->unsignedInteger('nombre_avis')->default(0);
            $table->timestamps();
        });

        // Les avis affichés sur le site (10 au plus), choisis au premier import.
        Schema::create('avis_google', function (Blueprint $table) {
            $table->id();
            $table->string('identifiant')->unique();
            $table->string('auteur');
            $table->unsignedTinyInteger('note');
            $table->text('texte');
            $table->timestamp('publie_le');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis_google');
        Schema::dropIfExists('note_google');
    }
};
