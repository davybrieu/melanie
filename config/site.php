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
    | L'adresse e-mail du site est celle d'expédition (MAIL_FROM_ADDRESS, dans
    | config/mail.php). WhatsApp utilise le numéro de téléphone. SITE_TELEPHONE
    | peut remplacer la valeur ci-dessous depuis le .env (laissé vide, c'est
    | cette valeur qui s'applique).
    |
    */

    'telephone' => env('SITE_TELEPHONE') ?: '06 16 39 92 96',

    'instagram' => 'mb_photographiiie',

    // Fiche Google (Google Business Profile), désignée par son identifiant de lieu
    // (Place ID). Comme le téléphone, GOOGLE_PLACE_ID le remplace depuis le .env.
    'google_place_id' => env('GOOGLE_PLACE_ID') ?: 'ChIJT5cm6jVu_0IRPk-l6R7IuVQ',

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
