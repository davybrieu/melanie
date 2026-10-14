<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

/**
 * Un bon cadeau créé dans l'espace de gestion. Son image à imprimer est dessinée dans le
 * navigateur (resources/js/admin/dessinerBonCadeau.js) : seules ses informations sont gardées.
 */
class BonCadeau extends Model
{
    use Prunable;

    /** Séances qui peuvent être offertes : les clés de resources/js/data/seances.js. */
    public const SEANCES = ['grossesse', 'nouveau-ne', 'famille'];

    // Sans caractères qui se confondent (0 et O, 1, I et L) : le numéro se dicte au téléphone.
    private const ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    protected $table = 'bons_cadeaux';

    protected $fillable = ['beneficiaire', 'offert_par', 'seance', 'message', 'emis_le', 'valable_jusqu_au', 'utilise_le'];

    protected function casts(): array
    {
        return [
            'emis_le' => 'date',
            'valable_jusqu_au' => 'date',
            'utilise_le' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BonCadeau $bon) {
            $bon->code ??= self::nouveauCode();
        });
    }

    /** Numéro unique, du type MP-7KQ4MZ. */
    public static function nouveauCode(): string
    {
        do {
            $code = 'MP-';

            for ($i = 0; $i < 6; $i++) {
                $code .= self::ALPHABET[random_int(0, strlen(self::ALPHABET) - 1)];
            }
        } while (self::where('code', $code)->exists());

        return $code;
    }

    /** valide, utilise ou expire, à la date du jour (en France). */
    public function statut(): string
    {
        if ($this->utilise_le !== null) {
            return 'utilise';
        }

        return $this->valable_jusqu_au->toDateString() < today('Europe/Paris')->toDateString() ? 'expire' : 'valide';
    }

    /**
     * Effacés trois ans après la fin de leur validité et leur utilisation (politique de
     * confidentialité), par la commande model:prune, lancée chaque jour (routes/console.php).
     */
    public function prunable(): Builder
    {
        $limite = now()->subYears(3)->toDateString();

        return static::where('valable_jusqu_au', '<', $limite)
            ->where(fn (Builder $requete) => $requete->whereNull('utilise_le')->orWhere('utilise_le', '<', $limite));
    }
}
