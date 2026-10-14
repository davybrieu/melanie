<?php

return [

    // Routes techniques inutiles côté Vue, et celles de l'espace de gestion : elles ne sont
    // pas exposées dans le HTML (les pages Admin/* écrivent leurs adresses elles-mêmes).
    'except' => ['_inertia.*', 'storage.*', 'photo', 'admin.*'],

    // @routes n'injecte que la liste des routes : la fonction route() (21 Ko) est déjà
    // dans le bundle JavaScript, via ZiggyVue (resources/js/app.js).
    'skip-route-function' => true,

];
