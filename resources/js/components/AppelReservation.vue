<script setup>
import { inject } from 'vue';
import Bouton from './Bouton.vue';
import Fleur from './Fleur.vue';
import Icone from './Icone.vue';
import Pinceau from './Pinceau.vue';

defineProps({
    titre: { type: String, default: 'Parlons de votre projet' },
    texte: {
        type: String,
        default: 'Racontez-moi votre histoire, vos envies, la période qui vous conviendrait : je vous réponds avec plaisir pour imaginer ensemble votre séance.',
    },
    seance: { type: String, default: null },
    // Lien et libellé du bouton principal (par défaut : formulaire de contact).
    href: { type: String, default: null },
    bouton: { type: String, default: 'Réserver ma séance' },
});

const route = inject('route');
</script>

<template>
    <section class="relative isolate overflow-hidden bg-creme-200 py-20 sm:py-28">
        <Fleur variante="gypsophile" class="absolute -top-6 -left-10 -z-10 w-40 opacity-80 sm:w-56" />
        <Fleur variante="pampa" class="absolute -right-10 -bottom-10 -z-10 w-36 opacity-70 sm:w-52" />

        <div class="conteneur text-center">
            <p>
                <span class="manuscrit relative isolate inline-block px-8 pt-3 pb-1 text-[2.6rem] text-creme-50 sm:text-5xl">
                    <Pinceau class="text-poudre-400" />
                    Réservation
                </span>
            </p>
            <h2 class="mt-5 text-[2rem] sm:text-5xl">{{ titre }}</h2>
            <p class="texte-courant mx-auto mt-5 max-w-xl">{{ texte }}</p>

            <div class="mt-9 flex flex-wrap justify-center gap-4">
                <Bouton :href="href ?? route('contact', seance ? { seance } : {})">{{ bouton }}</Bouton>
            </div>

            <p class="mt-8 inline-flex items-center gap-2.5 text-sm text-taupe-600">
                <Icone nom="voiture" class="size-5 text-or-500" />
                Je me déplace sur Dijon et alentours · Places limitées
            </p>
        </div>
    </section>
</template>
