<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Identité
    |--------------------------------------------------------------------------
    */

    'nom' => 'Mélanie Photographie',

    'metier' => 'Photographe grossesse, nouveau-né & famille',

    'naissance' => '1997-02-23',

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    |
    | L'adresse e-mail reçoit les demandes du formulaire de contact et figure
    | dans les mentions légales. WhatsApp utilise le numéro de téléphone.
    | SITE_TELEPHONE peut remplacer la valeur ci-dessous depuis le .env
    | (laissé vide, c'est cette valeur qui s'applique).
    |
    */

    'email' => env('SITE_EMAIL', 'contact@melanie-photographie.fr'),

    'telephone' => env('SITE_TELEPHONE') ?: '06 16 39 92 96',

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
