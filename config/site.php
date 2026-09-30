<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identité
    |--------------------------------------------------------------------------
    */

    'nom' => 'Mélanie Brieu',

    'marque' => 'MB Photographie',

    'metier' => 'Photographe grossesse, nouveau-né & famille',

    'naissance' => '1997-02-23',

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    |
    | L'adresse e-mail reçoit les demandes du formulaire de contact et figure
    | dans les mentions légales. Le téléphone n'est affiché que s'il est
    | renseigné.
    |
    */

    'email' => env('SITE_EMAIL', 'contact@melanie-photographie.fr'),

    'telephone' => env('SITE_TELEPHONE'),

    'instagram' => 'mb_photographiiie',

    // Délai de réponse annoncé sous le formulaire de contact (en heures).
    'delai_reponse' => (int) env('SITE_DELAI_REPONSE', 48),

    /*
    |--------------------------------------------------------------------------
    | Localisation
    |--------------------------------------------------------------------------
    */

    'ville' => 'Dijon',

    'commune' => 'Chenôve',

    'code_postal' => '21300',

    'departement' => "Côte-d'Or",

    'region' => 'Bourgogne-Franche-Comté',

];
