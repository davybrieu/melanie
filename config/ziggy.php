<?php

return [

    // Routes techniques inutiles côté Vue : elles ne sont pas exposées dans le HTML.
    'except' => ['_inertia.*', 'storage.*', 'photo'],

];
