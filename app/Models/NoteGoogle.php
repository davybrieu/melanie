<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Note globale et nombre d'avis de la fiche Google (une seule ligne), actualisés chaque jour
 * par la commande avis:actualiser.
 */
class NoteGoogle extends Model
{
    protected $table = 'note_google';

    protected $fillable = ['note', 'nombre_avis'];

    protected function casts(): array
    {
        return [
            'note' => 'float',
            'nombre_avis' => 'integer',
        ];
    }
}
