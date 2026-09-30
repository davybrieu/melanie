<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DemandeContactRequest extends FormRequest
{
    public const SEANCES = [
        'grossesse' => 'Grossesse',
        'nouveau-ne' => 'Nouveau-né',
        'famille' => 'Famille',
    ];

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'prenom' => ['required', 'string', 'max:80'],
            'nom' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:150'],
            'telephone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+().\s-]{6,}$/'],
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
            'email' => 'e-mail',
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
            'telephone.regex' => 'Le numéro de téléphone ne semble pas valide.',
        ];
    }
}
