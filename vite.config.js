import inertia from '@inertiajs/vite';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { local } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Polices servies par le site (aucun appel externe, RGPD) : fichiers WOFF2 de resources/fonts
            // (sous-ensemble latin de Bunny Fonts). Pas de bunny() : avec ses plages Unicode, le plugin
            // écrit une règle par format, et la règle WOFF, plus lourde, remplaçait la WOFF2 préchargée.
            // Graisses explicites : le plugin lit « normal » dans un nom de fichier comme 400.
            // optimizedFallbacks (paquet fontaine) : police système ajustée aux mêmes dimensions,
            // affichée le temps du chargement, pour que le texte ne bouge pas au changement de police.
            fonts: [
                local('Gilda Display', {
                    variants: [{ src: 'resources/fonts/gilda-display-400.woff2', weight: 400 }],
                    optimizedFallbacks: true,
                }),
                local('Jost', {
                    variants: [
                        { src: 'resources/fonts/jost-300.woff2', weight: 300 },
                        { src: 'resources/fonts/jost-400.woff2', weight: 400 },
                        { src: 'resources/fonts/jost-500.woff2', weight: 500 },
                    ],
                    preload: [{ weight: 300 }, { weight: 400 }],
                    optimizedFallbacks: true,
                }),
                local('Allison', {
                    variants: [{ src: 'resources/fonts/allison-400.woff2', weight: 400 }],
                    preload: false,
                    optimizedFallbacks: true,
                }),
            ],
        }),
        // Serveur SSR de production (php artisan inertia:start-ssr) : accessible seulement en local,
        // sur un port propre au site (13714, le port par défaut, est pris par d'autres sites du serveur).
        // Même port que dans config/inertia.php.
        inertia({ ssr: { port: 13728, host: '127.0.0.1' } }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
