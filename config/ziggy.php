<?php

return [

    // Routes techniques inutiles côté Vue : elles ne sont pas exposées dans le HTML.
    'except' => ['_inertia.*', 'storage.*', 'photo'],

    // @routes n'injecte que la liste des routes : la fonction route() (21 Ko) est déjà
    // dans le bundle JavaScript, via ZiggyVue (resources/js/app.js).
    'skip-route-function' => true,

];
