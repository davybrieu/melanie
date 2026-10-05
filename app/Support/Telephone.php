<?php

namespace App\Support;

/**
 * Numéros de téléphone saisis dans le formulaire de contact.
 *
 * Sont acceptés les numéros français à 10 chiffres (06 12 34 56 78, +33 6 12 34 56 78,
 * 0033…) hors numéros spéciaux en 08, et les numéros étrangers au format international
 * (+41 79 123 45 67). Les faux numéros évidents sont refusés.
 */
class Telephone
{
    /** Numéros d'exemple, souvent recopiés tels quels (dont celui du formulaire). */
    private const EXEMPLES = ['0612345678', '0712345678', '0123456789'];

    /**
     * Numéro sans espaces ni séparateurs : 0612345678 en France, +41791234567 à l'étranger.
     * Renvoie null si ce n'est pas un vrai numéro.
     */
    public static function normaliser(string $numero): ?string
    {
        // Espaces (y compris insécables), points, tirets, barres obliques et parenthèses
        $numero = preg_replace('/[\s\x{00A0}\x{202F}.\-\/()]/u', '', $numero) ?? '';
        $numero = preg_replace('/^00/', '+', $numero);

        // +33 6 12 34 56 78 ou +33 (0)6 12 34 56 78 → 06 12 34 56 78
        if (str_starts_with($numero, '+33')) {
            $numero = '0'.ltrim(substr($numero, 3), '0');
        }

        if (preg_match('/^0[1-79]\d{8}$/', $numero)) {
            // Huit derniers chiffres identiques (06 00 00 00 00) ou numéro d'exemple
            $faux = preg_match('/^\d{2}(\d)\1{7}$/', $numero) || in_array($numero, self::EXEMPLES, true);

            return $faux ? null : $numero;
        }

        // Étranger : indicatif puis 8 à 15 chiffres en tout (norme E.164), sans longue suite identique
        if (preg_match('/^\+[1-9]\d{7,14}$/', $numero) && ! preg_match('/(\d)\1{7,}$/', $numero)) {
            return $numero;
        }

        return null;
    }

    /** Numéro lisible : 06 12 34 56 78 (France) ou format international tel quel. */
    public static function affichage(string $normalise): string
    {
        return str_starts_with($normalise, '0') ? trim(chunk_split($normalise, 2, ' ')) : $normalise;
    }

    /** Lien d'appel : tel:+33612345678. */
    public static function lien(string $normalise): string
    {
        return 'tel:'.(str_starts_with($normalise, '0') ? '+33'.substr($normalise, 1) : $normalise);
    }
}
