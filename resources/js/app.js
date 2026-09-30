import { createInertiaApp } from '@inertiajs/vue3';
import { ZiggyVue } from 'ziggy-js';
import SiteLayout from './layouts/SiteLayout.vue';

// Ce fichier sert à la fois d'entrée client et d'entrée SSR : le plugin
// @inertiajs/vite résout les pages de ./pages et génère le serveur SSR.
createInertiaApp({
    title: (title) => (title ? `${title} | Mélanie Photographie` : 'Mélanie Photographie'),
    layout: () => SiteLayout,
    withApp(app, { ssr, page }) {
        // Dans le navigateur, Ziggy lit la config injectée par @routes ;
        // en SSR, elle arrive par la prop partagée `ziggy`.
        app.use(
            ZiggyVue,
            ssr ? { ...page.props.ziggy, location: new URL(page.props.ziggy.location) } : undefined,
        );
    },
    progress: {
        color: '#ab8556',
    },
});
