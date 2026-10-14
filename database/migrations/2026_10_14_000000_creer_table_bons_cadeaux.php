<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bons cadeaux créés dans l'espace de gestion (/admin/bons-cadeaux).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bons_cadeaux', function (Blueprint $table) {
            $table->id();
            // Numéro imprimé sur le bon (MP-7KQ4MZ), demandé à la réservation.
            $table->string('code', 16)->unique();
            $table->string('beneficiaire');
            $table->string('offert_par')->nullable();
            $table->string('seance', 20);
            $table->text('message')->nullable();
            $table->date('emis_le');
            $table->date('valable_jusqu_au');
            $table->date('utilise_le')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bons_cadeaux');
    }
};
