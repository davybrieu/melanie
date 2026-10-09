<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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

    /** Actualisée depuis moins de 30 jours : au-delà, les règles de Google interdisent de la garder. */
    public function scopeAJour(Builder $requete): void
    {
        $requete->where('updated_at', '>=', now()->subDays(30));
    }
}
