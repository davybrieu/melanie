<script setup>
// Éléments floraux détourés du logo et du flyer (purement décoratifs).
const fleurs = {
    gypsophile: { src: '/images/marque/fleurs-gypso-1.webp', largeur: 250, hauteur: 290 },
    'gypsophile-coeur': { src: '/images/marque/fleurs-gypso-2.webp', largeur: 206, hauteur: 214 },
    pampa: { src: '/images/marque/fleurs-pampa.webp', largeur: 276, hauteur: 405 },
};

defineProps({
    variante: { type: String, required: true },
});
</script>

<template>
    <img
        :src="fleurs[variante].src"
        :width="fleurs[variante].largeur"
        :height="fleurs[variante].hauteur"
        alt=""
        aria-hidden="true"
        loading="lazy"
        decoding="async"
        class="pointer-events-none select-none"
    />
</template>
