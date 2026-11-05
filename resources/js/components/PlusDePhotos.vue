<script setup>
import { inject } from 'vue';
import Bouton from './Bouton.vue';

// Sous la galerie d'une page séance (8 photos au plus) : invite à voir toutes les photos dans le
// portfolio de la séance, en disant combien n'apparaissent pas sur la page.
defineProps({
    categorie: { type: String, required: true },
    // Nom de la séance dans le texte : « grossesse », « nouveau-né », « famille ».
    nom: { type: String, required: true },
    // Photos du portfolio qui ne sont pas sur la page (PageController::seance).
    autres: { type: Number, default: 0 },
});

const route = inject('route');
</script>

<template>
    <div class="mt-12 text-center">
        <p v-if="autres" class="texte-courant">
            Et encore {{ autres }} {{ autres > 1 ? 'photos' : 'photo' }} {{ nom }} à découvrir dans mon portfolio.
        </p>
        <Bouton :href="route('portfolio.categorie', { categorie })" icone="image" :class="autres ? 'mt-6' : ''">Voir tout le portfolio {{ nom }}</Bouton>
    </div>
</template>
