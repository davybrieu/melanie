<?php

namespace App\Support;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Accès à l'espace de gestion (/admin) : une seule personne, avec l'adresse e-mail et le mot de
 * passe de config/admin.php (ADMIN_EMAIL, ADMIN_MOT_DE_PASSE). Sans eux, l'espace n'existe pas.
 */
class Admin
{
    public static function actif(): bool
    {
        return filled(config('admin.email')) && filled(config('admin.mot_de_passe'));
    }

    /** Mot de passe en clair dans le .env, ou empreinte bcrypt ($2y$…). */
    public static function verifier(string $email, string $motDePasse): bool
    {
        $attendu = (string) config('admin.mot_de_passe');

        $emailCorrect = hash_equals(Str::lower(trim((string) config('admin.email'))), Str::lower(trim($email)));
        $motDePasseCorrect = str_starts_with($attendu, '$2y$')
            ? Hash::check($motDePasse, $attendu)
            : hash_equals($attendu, $motDePasse);

        return $emailCorrect && $motDePasseCorrect;
    }

    /**
     * Jeton gardé dans la session après la connexion. Il dépend des identifiants : changer
     * l'adresse ou le mot de passe dans le .env déconnecte toutes les sessions ouvertes.
     */
    public static function jeton(): string
    {
        return hash_hmac('sha256', config('admin.email').'|'.config('admin.mot_de_passe'), (string) config('app.key'));
    }
}
