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
    | SITE_TELEPHONE et SITE_FACEBOOK peuvent remplacer les valeurs ci-dessous
    | depuis le .env (laissés vides, ce sont ces valeurs qui s'appliquent).
    |
    */

    'email' => env('SITE_EMAIL', 'contact@melanie-photographie.fr'),

    'telephone' => env('SITE_TELEPHONE') ?: '06 16 39 92 96',

    'instagram' => 'mb_photographiiie',

    // Adresse complète de la page Facebook (provisoire : la page n'est pas encore créée).
    'facebook' => env('SITE_FACEBOOK') ?: 'https://www.facebook.com/melaniephotographie.dijon',

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
