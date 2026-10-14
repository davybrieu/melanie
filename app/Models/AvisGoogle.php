<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Un avis de la fiche Google avec un texte, actualisé chaque jour par la commande
 * avis:actualiser. Il n'est affiché sur le site que si `affiche` est vrai (choix de l'espace de
 * gestion). De l'auteur, seuls le prénom et l'initiale du nom sont gardés.
 */
class AvisGoogle extends Model
{
    protected $table = 'avis_google';

    protected $fillable = ['identifiant', 'auteur', 'note', 'texte', 'publie_le', 'affiche'];

    protected function casts(): array
    {
        return [
            'note' => 'integer',
            'publie_le' => 'datetime',
            'affiche' => 'boolean',
        ];
    }

    /** Actualisés depuis moins de 30 jours : au-delà, les règles de Google interdisent de les garder. */
    public function scopeAJour(Builder $requete): void
    {
        $requete->where('updated_at', '>=', now()->subDays(30));
    }

    public function scopeAffiches(Builder $requete): void
    {
        $requete->where('affiche', true);
    }
}
