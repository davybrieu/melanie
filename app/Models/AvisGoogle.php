<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Un avis de la fiche Google affiché sur le site, actualisé chaque jour par la commande
 * avis:actualiser. De l'auteur, seuls le prénom et l'initiale du nom sont gardés.
 */
class AvisGoogle extends Model
{
    protected $table = 'avis_google';

    protected $fillable = ['identifiant', 'auteur', 'note', 'texte', 'publie_le'];

    protected function casts(): array
    {
        return [
            'note' => 'integer',
            'publie_le' => 'datetime',
        ];
    }

    /** Actualisés depuis moins de 30 jours : au-delà, les règles de Google interdisent de les garder. */
    public function scopeAJour(Builder $requete): void
    {
        $requete->where('updated_at', '>=', now()->subDays(30));
    }
}
