<script setup>
import { ref } from 'vue';
import Bouton from './Bouton.vue';
import Etoiles from './Etoiles.vue';
import TitreSection from './TitreSection.vue';
import { useSite } from '../composables/useSite';

defineProps({
    // { note, noteTexte, nombre, avis: [{ auteur, note, texte, date }] } : PageController, commande avis:actualiser.
    fiche: { type: Object, required: true },
});

const site = useSite();

// Les avis longs sont coupés ; « Lire la suite » les déplie.
const LONGUEUR_COUPEE = 300;
const deplies = ref([]);
</script>

<!-- Pas de photo de profil : l'afficher ferait charger une image depuis Google à chaque visite (RGPD). -->
<template>
    <section class="py-24 sm:py-32">
        <div class="conteneur">
            <TitreSection surtitre="Avis Google" titre="Ils m’ont fait confiance" />

            <div class="mt-8 flex flex-col items-center gap-2 text-center">
                <p class="flex items-center gap-4">
                    <span class="font-serif text-5xl text-cacao-800">{{ fiche.noteTexte }}</span>
                    <Etoiles :note="fiche.note" class="text-2xl" />
                </p>
                <p class="text-cacao-700">Note moyenne sur {{ fiche.nombre }} avis Google</p>
            </div>

            <!-- Sur mobile, un bandeau qui défile de côté (10 avis empilés feraient une page sans fin) ; au-delà, des colonnes. -->
            <ul
                v-if="fiche.avis.length"
                class="-mx-5 mt-14 flex snap-x snap-mandatory scroll-px-5 gap-4 overflow-x-auto px-5 pb-2 sm:-mx-8 sm:scroll-px-8 sm:px-8 md:mx-0 md:block md:columns-2 md:gap-5 md:overflow-visible md:px-0 md:pb-0 lg:columns-3"
            >
                <li
                    v-for="(avis, i) in fiche.avis"
                    :key="i"
                    class="flex w-[85%] shrink-0 snap-start flex-col rounded-2xl border border-creme-300 bg-creme-50 p-7 sm:w-[55%] md:mb-5 md:w-auto md:break-inside-avoid"
                >
                    <Etoiles :note="avis.note" class="text-lg" />
                    <p
                        class="mt-4 text-[0.97rem] leading-relaxed whitespace-pre-line text-cacao-700"
                        :class="{ 'line-clamp-6': avis.texte.length > LONGUEUR_COUPEE && !deplies.includes(i) }"
                    >
                        {{ avis.texte }}
                    </p>
                    <button
                        v-if="avis.texte.length > LONGUEUR_COUPEE"
                        type="button"
                        class="mt-2 self-start text-sm text-cacao-800 underline decoration-or-400 underline-offset-4 hover:decoration-cacao-800"
                        :aria-expanded="deplies.includes(i)"
                        @click="deplies.includes(i) ? deplies.splice(deplies.indexOf(i), 1) : deplies.push(i)"
                    >
                        {{ deplies.includes(i) ? 'Réduire' : 'Lire la suite' }}
                    </button>
                    <div class="mt-auto flex items-center gap-3 pt-6">
                        <span class="grid size-10 shrink-0 place-items-center rounded-full bg-poudre-100 font-serif text-lg text-cacao-800" aria-hidden="true">
                            {{ avis.auteur.charAt(0) }}
                        </span>
                        <p class="leading-tight">
                            <span class="block text-cacao-800">{{ avis.auteur }}</span>
                            <span class="text-sm text-taupe-600">{{ avis.date }}</span>
                        </p>
                    </div>
                </li>
            </ul>

            <p v-if="site.googleUrl" class="mt-12 text-center">
                <Bouton :href="site.googleUrl" externe variante="contour" icone="google">Voir tous les avis sur Google</Bouton>
            </p>
        </div>
    </section>
</template>
