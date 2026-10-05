<script setup>
import { computed } from 'vue';

// Éléments floraux détourés du logo et du flyer (purement décoratifs) : le texte
// alternatif les décrit, aria-hidden les retire de la lecture d'écran.
// Par défaut, pas de chargement différé (lazy) : en haut de page, une fleur peut être le plus
// grand élément affiché (LCP), et la différer retarde tout l'affichage.
const fleurs = {
    gypsophile: { src: '/images/marque/fleurs-gypso-1.webp', largeur: 250, hauteur: 290, alt: 'Branches de gypsophile à l’aquarelle' },
    'gypsophile-coeur': { src: '/images/marque/fleurs-gypso-2.webp', largeur: 206, hauteur: 214, alt: 'Brin de gypsophile et cœur dessiné à la main' },
    pampa: { src: '/images/marque/fleurs-pampa.webp', largeur: 276, hauteur: 405, alt: 'Bouquet d’herbes de la pampa et de gypsophile séchées' },
};

// Points de rupture de Tailwind (sm, md, lg).
const largeursMinimales = { sm: '40rem', md: '48rem', lg: '64rem' };

// Image vide (GIF transparent de 1 px) : ce que reçoit un écran trop étroit pour la fleur.
const imageVide = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

const props = defineProps({
    variante: { type: String, required: true },
    // Fleur en haut de page qui est le plus grand élément affiché sur mobile : chargée en priorité.
    prioritaire: { type: Boolean, default: false },
    // Fleur toujours loin sous le haut de page (appel à réserver, pied de page) : chargée à l'approche.
    differee: { type: Boolean, default: false },
    // Fleur masquée sur les petits écrans (classe « hidden md:block ») : 'sm', 'md' ou 'lg'.
    // Le navigateur télécharge une image même masquée : sous ce point de rupture, il ne reçoit rien.
    visibleDes: { type: String, default: null },
});

const fleur = computed(() => fleurs[props.variante]);
</script>

<template>
    <picture v-if="visibleDes" class="pointer-events-none select-none">
        <source :media="`(min-width: ${largeursMinimales[visibleDes]})`" :srcset="fleur.src" :width="fleur.largeur" :height="fleur.hauteur" />
        <img :src="imageVide" :width="fleur.largeur" :height="fleur.hauteur" :alt="fleur.alt" aria-hidden="true" decoding="async" class="block h-auto w-full" />
    </picture>
    <img
        v-else
        :src="fleur.src"
        :width="fleur.largeur"
        :height="fleur.hauteur"
        :alt="fleur.alt"
        aria-hidden="true"
        :fetchpriority="prioritaire ? 'high' : 'auto'"
        :loading="differee ? 'lazy' : undefined"
        decoding="async"
        class="pointer-events-none select-none"
    />
</template>
