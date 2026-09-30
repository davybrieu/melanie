<script setup>
import { computed, inject } from 'vue';
import Bouton from '../components/Bouton.vue';
import Fleur from '../components/Fleur.vue';
import Seo from '../components/Seo.vue';

const props = defineProps({
    statut: { type: Number, required: true },
});

const route = inject('route');

const messages = {
    403: { titre: 'Accès réservé', texte: 'Cette page n’est pas accessible.' },
    404: { titre: 'Cette page s’est envolée…', texte: 'Elle a peut-être changé d’adresse. Les plus beaux moments, eux, sont toujours là.' },
    429: { titre: 'Doucement…', texte: 'Trop de demandes en peu de temps. Patientez une minute avant de réessayer.' },
    500: { titre: 'Un petit souci technique', texte: 'Le site rencontre une erreur. Réessayez dans quelques instants.' },
    503: { titre: 'Pause douceur', texte: 'Le site est en maintenance, il revient très vite.' },
};

const message = computed(() => messages[props.statut] ?? messages[500]);
</script>

<template>
    <Seo :titre="message.titre" :description="message.texte" :indexer="false" />

    <section class="relative isolate overflow-hidden py-24 sm:py-32">
        <Fleur variante="gypsophile" class="absolute top-0 -left-12 -z-10 w-48 opacity-70 sm:w-64" />
        <Fleur variante="pampa" class="absolute -right-10 bottom-0 -z-10 w-40 opacity-60 sm:w-56" />
        <div class="conteneur max-w-2xl text-center">
            <p class="font-script text-[6rem] leading-none text-or-500">{{ statut }}</p>
            <h1 class="mt-4 text-4xl sm:text-5xl">{{ message.titre }}</h1>
            <p class="texte-courant mt-5">{{ message.texte }}</p>
            <div class="mt-10 flex flex-wrap justify-center gap-4">
                <Bouton :href="route('accueil')">Retour à l’accueil</Bouton>
                <Bouton :href="route('contact')" variante="contour">Me contacter</Bouton>
            </div>
        </div>
    </section>
</template>
