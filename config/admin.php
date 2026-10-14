<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Espace de gestion (/admin)
    |--------------------------------------------------------------------------
    |
    | Création et suivi des bons cadeaux, réservés à Mélanie. Une seule
    | personne s'y connecte, avec cette adresse e-mail et ce mot de passe
    | (en clair, ou empreinte bcrypt). Tant que l'un des deux est vide,
    | l'espace n'existe pas : ses adresses répondent 404.
    |
    */

    'email' => env('ADMIN_EMAIL'),

    'mot_de_passe' => env('ADMIN_MOT_DE_PASSE'),

];
