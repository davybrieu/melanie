<?php

namespace App\Http\Requests;

use App\Support\Photos;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

/**
 * Photo ajoutée dans l'espace de gestion, ou son nouveau texte alternatif (PATCH). Ce texte,
 * obligatoire, décrit la photo à Google et aux lecteurs d'écran.
 */
class PhotoRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge(['alt' => preg_replace('/\s+/u', ' ', trim((string) $this->input('alt')))]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $alt = ['required', 'string', 'min:10', 'max:125'];

        if ($this->isMethod('patch')) {
            return ['alt' => $alt];
        }

        return [
            'dossier' => ['required', Rule::in(Photos::DOSSIERS)],
            'emplacement' => ['nullable', 'required_if:dossier,site', 'prohibited_unless:dossier,site', Rule::in(Photos::EMPLACEMENTS)],
            'photo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:15360'],
            'alt' => $alt,
        ];
    }

    /**
     * Dimensions : assez grande pour rester nette, pas au point de saturer la mémoire du serveur.
     *
     * @return list<callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (! $this->hasFile('photo') || $validator->errors()->has('photo')) {
                    return;
                }

                [$largeur, $hauteur] = app(Photos::class)->dimensions($this->file('photo')->getRealPath()) ?? [0, 0];

                if ($largeur * $hauteur > Photos::PIXELS_MAXIMUM) {
                    $validator->errors()->add('photo', 'Cette photo est trop lourde à traiter : exportez-la en 2400 px sur le plus grand côté, puis réessayez.');
                } elseif (max($largeur, $hauteur) < Photos::COTE_MINIMUM) {
                    $validator->errors()->add('photo', $largeur
                        ? "Cette photo est trop petite ({$largeur} × {$hauteur} px) : il faut au moins ".Photos::COTE_MINIMUM.' px sur le plus grand côté pour qu’elle reste nette.'
                        : 'Cette photo est illisible : enregistrez-la en JPEG, puis réessayez.');
                }
            },
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return ['alt' => 'texte alternatif'];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'photo.mimes' => 'La photo doit être au format JPEG, PNG ou WebP.',
            'photo.uploaded' => 'La photo n’a pas pu être envoyée : elle dépasse la taille acceptée par le serveur.',
            'photo.max' => 'La photo dépasse 15 Mo : exportez-la en 2400 px sur le plus grand côté, puis réessayez.',
            'alt.required' => 'Décrivez la photo : ce texte est lu par Google et par les lecteurs d’écran.',
            'alt.min' => 'Décrivez un peu plus la photo (10 caractères au moins).',
            'alt.max' => 'Le texte alternatif ne doit pas dépasser 125 caractères.',
        ];
    }
}
