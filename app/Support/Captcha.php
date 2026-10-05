<?php

namespace App\Support;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * hCaptcha, sur le formulaire de contact (clés dans config/services.php, depuis le .env).
 *
 * Sans clés, la vérification est désactivée : le formulaire reste utilisable en local.
 * Clés de test d'hCaptcha (toujours acceptées) : site 10000000-ffff-ffff-ffff-000000000001,
 * secret 0x0000000000000000000000000000000000000000.
 */
class Captcha
{
    private const URL_VERIFICATION = 'https://api.hcaptcha.com/siteverify';

    public static function actif(): bool
    {
        return filled(config('services.hcaptcha.cle')) && filled(config('services.hcaptcha.secret'));
    }

    /** Clé publique, transmise à la page de contact (null si hCaptcha est désactivé). */
    public static function cle(): ?string
    {
        return self::actif() ? config('services.hcaptcha.cle') : null;
    }

    /**
     * Vérifie le jeton auprès d'hCaptcha. Un jeton ne sert qu'une fois. L'adresse IP, facultative,
     * n'est pas transmise : hCaptcha la reçoit déjà quand la case est cochée dans le navigateur.
     * Si hCaptcha ne répond pas, la demande passe quand même (le champ piège et la
     * limite d'envois restent actifs) : mieux vaut un spam qu'une cliente perdue.
     */
    public static function verifier(string $jeton): bool
    {
        try {
            $reponse = Http::asForm()->timeout(5)->post(self::URL_VERIFICATION, [
                'secret' => config('services.hcaptcha.secret'),
                'response' => $jeton,
                'sitekey' => config('services.hcaptcha.cle'),
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('hCaptcha injoignable : demande de contact acceptée sans vérification.', ['erreur' => $exception->getMessage()]);

            return true;
        }

        if ($reponse->failed()) {
            Log::warning('hCaptcha a répondu en erreur : demande de contact acceptée sans vérification.', ['statut' => $reponse->status()]);

            return true;
        }

        return $reponse->json('success') === true;
    }
}
