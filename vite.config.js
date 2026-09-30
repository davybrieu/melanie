import inertia from '@inertiajs/vite';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
            // Polices téléchargées au build et servies par le site (aucun appel externe, RGPD).
            fonts: [
                bunny('Gilda Display', {
                    weights: [400],
                    optimizedFallbacks: false,
                }),
                bunny('Jost', {
                    weights: [300, 400, 500],
                    preload: [{ weight: 300 }, { weight: 400 }],
                    optimizedFallbacks: false,
                }),
                bunny('Allison', {
                    weights: [400],
                    preload: false,
                    optimizedFallbacks: false,
                }),
            ],
        }),
        inertia(),
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
