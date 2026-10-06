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
    | L'adresse e-mail publique figure dans les mentions légales et la politique
    | de confidentialité, où la loi l'impose. Les demandes du formulaire partent
    | vers une adresse privée (SITE_EMAIL_DEMANDES, la boîte personnelle de
    | Mélanie), jamais envoyée aux pages (voir HandleInertiaRequests) ; laissée
    | vide, c'est l'adresse publique qui les reçoit. WhatsApp utilise le numéro
    | de téléphone. SITE_TELEPHONE peut remplacer la valeur ci-dessous depuis le
    | .env (laissé vide, c'est cette valeur qui s'applique).
    |
    */

    'email' => env('SITE_EMAIL', 'contact@melanie-photographie.fr'),

    'email_demandes' => env('SITE_EMAIL_DEMANDES') ?: env('SITE_EMAIL', 'contact@melanie-photographie.fr'),

    'telephone' => env('SITE_TELEPHONE') ?: '06 16 39 92 96',

    'instagram' => 'mb_photographiiie',

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
