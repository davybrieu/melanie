<?php

namespace App\Http\Requests;

use App\Support\Telephone;
use Closure;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'date_accouchement' => ['nullable', 'date'],
            'nombre_personnes' => ['nullable', 'integer', 'min:1', 'max:30'],
            'periode' => ['nullable', 'string', 'max:120'],
            'message' => ['nullable', 'string', 'max:3000'],
            // Champ invisible : seuls les robots le remplissent.
            'site_web' => ['prohibited'],
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
            'date_accouchement' => "date prévue d'accouchement",
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
        ];
    }
}
