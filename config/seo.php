<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Pages indexables
    |--------------------------------------------------------------------------
    |
    | Pages listées dans sitemap.xml (commande « seo:generer »), dans l'ordre
    | du silo. Toute nouvelle page publique doit être ajoutée ici (et dans
    | public/llms.txt, rédigé à la main).
    |
    */

    'pages' => [
        ['route' => 'accueil'],
        ['route' => 'grossesse'],
        ['route' => 'nouveau-ne'],
        ['route' => 'famille'],
        ['route' => 'portfolio'],
        ['route' => 'portfolio.categorie', 'parametres' => ['categorie' => 'grossesse']],
        ['route' => 'portfolio.categorie', 'parametres' => ['categorie' => 'nouveau-ne']],
        ['route' => 'portfolio.categorie', 'parametres' => ['categorie' => 'famille']],
        ['route' => 'tarifs'],
        ['route' => 'bon-cadeau'],
        ['route' => 'faq'],
        ['route' => 'a-propos'],
        ['route' => 'contact'],
        ['route' => 'mentions-legales'],
        ['route' => 'confidentialite'],
        ['route' => 'cgv'],
    ],

];
