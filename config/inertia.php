<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Rendu côté serveur (SSR)
    |--------------------------------------------------------------------------
    |
    | Seule cette section de la configuration d'Inertia est redéfinie : les
    | autres options gardent les valeurs du paquet
    | (vendor/inertiajs/inertia-laravel/config/inertia.php).
    |
    | Le serveur SSR du site écoute sur le port 13728 et non sur 13714, le port
    | par défaut d'Inertia, déjà pris par d'autres sites du serveur. Ce port est
    | compilé dans le bundle SSR : il doit rester identique à celui de
    | vite.config.js, d'où une valeur écrite ici et non dans le .env.
    |
    */

    'ssr' => [

        'enabled' => (bool) env('INERTIA_SSR_ENABLED', true),

        'runtime' => env('INERTIA_SSR_RUNTIME', 'node'),

        'ensure_runtime_exists' => (bool) env('INERTIA_SSR_ENSURE_RUNTIME_EXISTS', false),

        'url' => 'http://127.0.0.1:13728',

        'hot_url' => env('INERTIA_SSR_HOT_URL'),

        'timeout' => env('INERTIA_SSR_TIMEOUT'),

        'ensure_bundle_exists' => (bool) env('INERTIA_SSR_ENSURE_BUNDLE_EXISTS', true),

        'throw_on_error' => (bool) env('INERTIA_SSR_THROW_ON_ERROR', false),

    ],

];
