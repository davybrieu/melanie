<script setup>
// Éléments floraux détourés du logo et du flyer (purement décoratifs) : le texte
// alternatif les décrit, aria-hidden les retire de la lecture d'écran.
// Pas de chargement différé (lazy) : en haut de page, une fleur peut être le plus grand
// élément affiché (LCP), et le différer retarde tout l'affichage.
const fleurs = {
    gypsophile: { src: '/images/marque/fleurs-gypso-1.webp', largeur: 250, hauteur: 290, alt: 'Branches de gypsophile à l’aquarelle' },
    'gypsophile-coeur': { src: '/images/marque/fleurs-gypso-2.webp', largeur: 206, hauteur: 214, alt: 'Brin de gypsophile et cœur dessiné à la main' },
    pampa: { src: '/images/marque/fleurs-pampa.webp', largeur: 276, hauteur: 405, alt: 'Bouquet d’herbes de la pampa et de gypsophile séchées' },
};

defineProps({
    variante: { type: String, required: true },
    // Fleur en haut de page qui est le plus grand élément affiché sur mobile : chargée en priorité.
    prioritaire: { type: Boolean, default: false },
});
</script>

<template>
    <img
        :src="fleurs[variante].src"
        :width="fleurs[variante].largeur"
        :height="fleurs[variante].hauteur"
        :alt="fleurs[variante].alt"
        aria-hidden="true"
        :fetchpriority="prioritaire ? 'high' : 'auto'"
        decoding="async"
        class="pointer-events-none select-none"
    />
</template>
