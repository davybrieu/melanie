<script setup>
import { euros } from '../data/seances';
import Icone from './Icone.vue';
import Pinceau from './Pinceau.vue';

// Carte tarif inspirée du flyer : pictogramme, titre, accroche manuscrite, inclus, prix.
defineProps({
    offre: { type: Object, required: true },
    balise: { type: String, default: 'h3' },
});
</script>

<template>
    <article
        class="relative grid gap-6 rounded-2xl border border-creme-300 bg-creme-50 p-6 shadow-[0_18px_40px_-28px_rgba(75,52,40,0.35)] sm:grid-cols-[auto_1fr_auto] sm:items-center sm:gap-8 sm:p-8"
    >
        <div class="grid size-20 place-items-center rounded-full bg-poudre-100 text-cacao-800">
            <Icone :nom="offre.icone" class="size-10" />
        </div>

        <div>
            <component :is="balise" class="text-[1.35rem] tracking-[0.1em] uppercase">{{ offre.titre }}</component>
            <p class="mt-1">
                <span class="manuscrit relative isolate inline-block px-5 pt-2 text-[2rem]">
                    <Pinceau class="text-poudre-100" />
                    {{ offre.accroche }}
                </span>
            </p>
            <ul class="mt-4 space-y-1.5 text-[0.98rem] text-cacao-700">
                <li v-for="ligne in offre.inclus" :key="ligne" class="flex items-center gap-3">
                    <Icone nom="coeur" class="size-3.5 shrink-0 fill-poudre-400 text-poudre-400" />
                    {{ ligne }}
                </li>
            </ul>
        </div>

        <div class="flex items-center justify-center sm:h-full sm:border-l sm:border-creme-300 sm:pl-8">
            <p class="relative isolate px-4 font-serif text-[3.25rem] leading-none text-brique">
                <Pinceau class="text-poudre-100" />
                {{ euros(offre.prix) }}
            </p>
        </div>

        <Icone nom="coeur" class="absolute top-5 right-5 size-5 text-poudre-400" />
    </article>
</template>
