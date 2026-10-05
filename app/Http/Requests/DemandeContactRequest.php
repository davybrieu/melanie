<?php

namespace App\Http\Requests;

use App\Support\Captcha;
use App\Support\Telephone;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class DemandeContactRequest extends FormRequest
{
    /** Choix du formulaire : les trois séances du site, plus « Autres » (projet décrit dans le message). */
    public const SEANCES = [
        'grossesse' => 'Grossesse',
        'nouveau-ne' => 'Nouveau-né',
        'famille' => 'Famille',
        'autres' => 'Autres',
    ];

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'prenom' => ['required', 'string', 'max:80'],
            'nom' => ['required', 'string', 'max:80'],
            // Pas d'e-mail : Mélanie rappelle le client. Le numéro doit être un vrai numéro (App\Support\Telephone).
            'telephone' => ['required', 'string', 'max:30', function (string $attribut, mixed $valeur, Closure $echec) {
                if (! is_string($valeur) || Telephone::normaliser($valeur) === null) {
                    $echec('Ce numéro ne semble pas valide : indiquez vos 10 chiffres (06, 07…) ou le format international (+33…).');
                }
            }],
            'seances' => ['required', 'array', 'min:1'],
            'seances.*' => ['string', Rule::in(array_keys(self::SEANCES))],
            'nombre_personnes' => ['nullable', 'integer', 'min:1', 'max:30'],
            'periode' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:3000'],
            // Champ invisible : seuls les robots le remplissent.
            'site_web' => ['prohibited'],
            // Jeton hCaptcha, vérifié dans after() une fois les autres champs valides.
            'captcha' => Captcha::actif() ? ['required', 'string'] : ['nullable'],
        ];
    }

    /**
     * Vérification hCaptcha, seulement si le reste du formulaire est valide : un jeton ne sert
     * qu'une fois, et la cliente qui corrige un champ n'a pas à refaire la vérification.
     *
     * @return array<int, Closure>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! Captcha::actif() || $validator->errors()->isNotEmpty()) {
                    return;
                }

                if (! Captcha::verifier((string) $this->input('captcha'))) {
                    $validator->errors()->add('captcha', 'La vérification anti-robot a échoué ou expiré : cochez à nouveau la case.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'prenom' => 'prénom',
            'nom' => 'nom',
            'telephone' => 'téléphone',
            'seances' => 'type de séance',
            'seances.*' => 'type de séance',
            'nombre_personnes' => 'nombre de personnes',
            'periode' => 'période souhaitée',
            'message' => 'message',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'seances.required' => 'Choisissez au moins un type de séance.',
            'telephone.required' => 'Indiquez votre numéro de téléphone : je vous rappelle rapidement.',
            'message.required' => 'Dites-m’en un peu plus sur votre projet : quelques mots suffisent.',
            'captcha.required' => 'Cochez la case « Je suis un humain » avant d’envoyer votre demande.',
        ];
    }
}
