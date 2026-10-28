<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Une photo du site, ajoutée dans l'espace de gestion (/admin/photos). Son fichier d'origine est
 * gardé dans la table photos_originaux ; les pages reçoivent des variantes WebP (App\Support\Photos).
 *
 * Photo d'une séance (dossier grossesse, nouveau-ne ou famille) : son rôle dépend de sa position.
 * Photo du site (dossier site) : son emplacement (accueil, portrait, bon-cadeau) dit où elle va.
 */
class Photo extends Model
{
    protected $fillable = ['dossier', 'emplacement', 'position', 'nom', 'empreinte', 'largeur', 'hauteur', 'orientation', 'alt'];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'largeur' => 'integer',
            'hauteur' => 'integer',
            'orientation' => 'integer',
        ];
    }

    /** Photos d'une séance, dans l'ordre du site. */
    public function scopeSeance(Builder $requete, string $dossier): void
    {
        $requete->where('dossier', $dossier)->whereNull('emplacement')->orderBy('position')->orderBy('id');
    }
}
