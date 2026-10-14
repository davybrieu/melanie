<?php

namespace App\Http\Requests;

use App\Models\BonCadeau;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Création ou modification d'un bon cadeau (espace de gestion). Les longueurs maximales sont
 * celles qui tiennent sur l'image imprimée.
 */
class BonCadeauRequest extends FormRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'beneficiaire' => ['required', 'string', 'max:50'],
            'offert_par' => ['nullable', 'string', 'max:50'],
            'seance' => ['required', 'string', Rule::in(BonCadeau::SEANCES)],
            'message' => ['nullable', 'string', 'max:200'],
            'emis_le' => ['required', 'date_format:Y-m-d'],
            'valable_jusqu_au' => ['required', 'date_format:Y-m-d', 'after_or_equal:emis_le'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'beneficiaire' => 'bénéficiaire',
            'offert_par' => 'de la part de',
            'seance' => 'séance',
            'message' => 'message',
            'emis_le' => 'date d’émission',
            'valable_jusqu_au' => 'date de fin de validité',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'beneficiaire.required' => 'Indiquez à qui le bon est offert.',
            'seance.required' => 'Choisissez la séance offerte.',
            'valable_jusqu_au.after_or_equal' => 'La fin de validité ne peut pas précéder la date d’émission.',
        ];
    }
}
