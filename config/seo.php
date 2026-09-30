<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pages publiées
    |--------------------------------------------------------------------------
    |
    | Pages reprises dans sitemap.xml et llms.txt (commande « seo:generer »),
    | dans l'ordre du silo. « section » range la page dans llms.txt ;
    | l'accueil n'en a pas : il sert de résumé en tête du fichier.
    |
    */

    'pages' => [
        ['route' => 'accueil'],
        ['route' => 'grossesse', 'section' => 'Séances'],
        ['route' => 'nouveau-ne', 'section' => 'Séances'],
        ['route' => 'famille', 'section' => 'Séances'],
        ['route' => 'portfolio', 'section' => 'Portfolio'],
        ['route' => 'portfolio.categorie', 'parametres' => ['categorie' => 'grossesse'], 'section' => 'Portfolio'],
        ['route' => 'portfolio.categorie', 'parametres' => ['categorie' => 'nouveau-ne'], 'section' => 'Portfolio'],
        ['route' => 'portfolio.categorie', 'parametres' => ['categorie' => 'famille'], 'section' => 'Portfolio'],
        ['route' => 'tarifs', 'section' => 'Informations pratiques'],
        ['route' => 'bon-cadeau', 'section' => 'Informations pratiques'],
        ['route' => 'faq', 'section' => 'Informations pratiques'],
        ['route' => 'a-propos', 'section' => 'Informations pratiques'],
        ['route' => 'contact', 'section' => 'Informations pratiques'],
        ['route' => 'mentions-legales', 'section' => 'Optional'],
        ['route' => 'confidentialite', 'section' => 'Optional'],
        ['route' => 'cgv', 'section' => 'Optional'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Empreintes des pages
    |--------------------------------------------------------------------------
    |
    | Fichier (disque « local ») mémorisant l'empreinte du contenu de chaque
    | page et la date de sa dernière modification (<lastmod> du sitemap).
    |
    */

    'empreintes' => 'seo/empreintes.json',

    // Fuseau des dates <lastmod> et de l'exécution quotidienne.
    'fuseau_horaire' => 'Europe/Paris',

];
