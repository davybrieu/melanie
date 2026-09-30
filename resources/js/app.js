import { createInertiaApp } from '@inertiajs/vue3';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Ce fichier sert à la fois d'entrée client et d'entrée SSR : le plugin
// @inertiajs/vite résout les pages de ./pages et génère le serveur SSR.
createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    progress: {
        color: '#4B5563',
    },
});
