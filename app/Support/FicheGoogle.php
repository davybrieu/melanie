<?php

namespace App\Support;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Fiche Google de Mélanie, lue par la Business Profile API : note globale, nombre d'avis et avis.
 *
 * L'accès passe par OAuth, avec le compte propriétaire de la fiche (config/services.php, depuis
 * le .env) : identifiant et secret du client OAuth, et jeton d'actualisation obtenu une fois pour
 * toutes (portée business.manage). La fiche est retrouvée parmi celles du compte par son
 * Place ID (config/site.php).
 */
class FicheGoogle
{
    private const ETOILES = ['ONE' => 1, 'TWO' => 2, 'THREE' => 3, 'FOUR' => 4, 'FIVE' => 5];

    public static function actif(): bool
    {
        return filled(config('services.google_business.client_id'))
            && filled(config('services.google_business.client_secret'))
            && filled(config('services.google_business.refresh_token'));
    }

    /**
     * Tous les avis de la fiche, avec la note globale et le nombre d'avis.
     *
     * @return array{note: ?float, nombre: int, avis: list<array{identifiant: string, auteur: string, note: int, texte: string, publie_le: string}>}
     */
    public function lire(): array
    {
        $jeton = $this->jeton();
        $fiche = $this->fiche($jeton);
        $avis = [];
        $page = null;

        do {
            $reponse = $this->client($jeton)
                ->get("https://mybusiness.googleapis.com/v4/{$fiche}/reviews", array_filter(['pageSize' => 50, 'pageToken' => $page]))
                ->throw();

            foreach ($reponse->json('reviews', []) as $avisGoogle) {
                $avis[] = $this->avis($avisGoogle);
            }

            $page = $reponse->json('nextPageToken');
        } while ($page);

        return [
            'note' => $reponse->json('averageRating'),
            'nombre' => (int) $reponse->json('totalReviewCount', 0),
            'avis' => $avis,
        ];
    }

    /** Jeton d'accès d'une heure, obtenu avec le jeton d'actualisation. */
    private function jeton(): string
    {
        return Http::asForm()->timeout(20)->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'refresh_token',
            'client_id' => config('services.google_business.client_id'),
            'client_secret' => config('services.google_business.client_secret'),
            'refresh_token' => config('services.google_business.refresh_token'),
        ])->throw()->json('access_token');
    }

    /** Chemin de la fiche pour l'API des avis (accounts/…/locations/…), retrouvée par son Place ID. */
    private function fiche(string $jeton): string
    {
        $placeId = config('site.google_place_id');
        $comptes = $this->client($jeton)
            ->get('https://mybusinessaccountmanagement.googleapis.com/v1/accounts')
            ->throw()->json('accounts', []);

        foreach ($comptes as $compte) {
            $fiches = $this->client($jeton)
                ->get("https://mybusinessbusinessinformation.googleapis.com/v1/{$compte['name']}/locations", ['readMask' => 'name,metadata', 'pageSize' => 100])
                ->throw()->json('locations', []);

            foreach ($fiches as $fiche) {
                if (($fiche['metadata']['placeId'] ?? null) === $placeId) {
                    return "{$compte['name']}/{$fiche['name']}";
                }
            }
        }

        throw new RuntimeException("Aucune fiche du compte Google n’a le Place ID {$placeId} (GOOGLE_PLACE_ID).");
    }

    /**
     * @param  array<string, mixed>  $avis
     * @return array{identifiant: string, auteur: string, note: int, texte: string, publie_le: string}
     */
    private function avis(array $avis): array
    {
        return [
            'identifiant' => $avis['reviewId'],
            'auteur' => $this->auteur($avis['reviewer'] ?? []),
            'note' => self::ETOILES[$avis['starRating'] ?? ''] ?? 0,
            'texte' => $this->texte($avis['comment'] ?? ''),
            'publie_le' => Carbon::parse($avis['createTime'])->utc()->toDateTimeString(),
        ];
    }

    /**
     * Prénom et initiale du nom (« Camille M. ») : rien de plus n'est gardé ni affiché.
     *
     * @param  array<string, mixed>  $auteur
     */
    private function auteur(array $auteur): string
    {
        $mots = preg_split('/\s+/u', trim($auteur['displayName'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);

        if (($auteur['isAnonymous'] ?? false) || $mots === []) {
            return 'Anonyme';
        }

        return count($mots) === 1 ? $mots[0] : $mots[0].' '.mb_strtoupper(mb_substr(end($mots), 0, 1)).'.';
    }

    /**
     * Texte de l'avis. Pour un avis écrit dans une autre langue, Google joint sa traduction
     * (« (Translated by Google) … (Original) … ») : seul le texte d'origine est gardé.
     */
    private function texte(string $texte): string
    {
        if (str_contains($texte, '(Original)')) {
            $texte = Str::afterLast($texte, '(Original)');
        } elseif (str_contains($texte, '(Translated by Google)')) {
            $texte = Str::before($texte, '(Translated by Google)');
        }

        return trim($texte);
    }

    private function client(string $jeton): PendingRequest
    {
        return Http::withToken($jeton)->acceptJson()->timeout(20);
    }
}
