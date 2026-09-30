<script setup>
import { router } from '@inertiajs/vue3';
import Icone from './Icone.vue';

// Accordéon natif <details> : réponses présentes dans le HTML (SEO) et utilisables sans JavaScript.
defineProps({
    questions: { type: Array, required: true },
});

// Les liens internes des réponses (HTML) naviguent sans recharger la page.
function suivreLien(evenement) {
    const lien = evenement.target.closest('a[href^="/"]');

    if (lien && !evenement.metaKey && !evenement.ctrlKey) {
        evenement.preventDefault();
        router.visit(lien.getAttribute('href'));
    }
}
</script>

<template>
    <div class="divide-y divide-creme-300 border-y border-creme-300" @click="suivreLien">
        <details v-for="item in questions" :key="item.q" class="group">
            <summary class="flex cursor-pointer list-none items-start justify-between gap-6 py-5 [&::-webkit-details-marker]:hidden">
                <h3 class="text-lg leading-snug text-cacao-800 transition-colors group-hover:text-brique sm:text-xl">{{ item.q }}</h3>
                <span class="mt-0.5 grid size-7 shrink-0 place-items-center rounded-full border border-or-400/80 text-or-600 transition-transform duration-300 group-open:rotate-45">
                    <Icone nom="plus" class="size-3.5" />
                </span>
            </summary>
            <div class="prose-mp pr-2 pb-7 sm:pr-14" v-html="item.r"></div>
        </details>
    </div>
</template>
