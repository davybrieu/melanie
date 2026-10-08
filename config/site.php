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

    // Fiche Google (Google Business Profile), désignée par son CID, tiré du lien de
    // partage Google Maps. En texte : le nombre dépasse la précision de JavaScript.
    // Place ID de la même fiche : ChIJT5cm6jVu_0IRPk-l6R7IuVQ.
    'google_cid' => '6105130804971917118',

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
