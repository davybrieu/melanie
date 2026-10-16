<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import Bouton from './Bouton.vue';
import Etoiles from './Etoiles.vue';
import Icone from './Icone.vue';
import TitreSection from './TitreSection.vue';
import { useSite } from '../composables/useSite';

defineProps({
    // { note, noteTexte, nombre, avis: [{ auteur, note, texte, date }] } : PageController, commande avis:actualiser.
    // Texte vide : note sans commentaire.
    fiche: { type: Object, required: true },
});

const site = useSite();

// Les avis longs sont coupés ; « Lire la suite » les déplie.
const LONGUEUR_COUPEE = 300;
const deplies = ref([]);

// Bandeau mobile : une flèche de chaque côté pour faire défiler les avis d'une carte, cachée
// quand le bandeau est au bout de ce côté.
const liste = ref(null);
const peutReculer = ref(false);
const peutAvancer = ref(false);
let observateur = null;

function majFleches() {
    const bandeau = liste.value;

    if (!bandeau) return;

    peutReculer.value = bandeau.scrollLeft > 2;
    peutAvancer.value = Math.ceil(bandeau.scrollLeft + bandeau.clientWidth) < bandeau.scrollWidth - 2;
}

function defiler(sens) {
    const bandeau = liste.value;
    const carte = bandeau?.firstElementChild;

    if (!carte) return;

    const pas = carte.getBoundingClientRect().width + (parseFloat(getComputedStyle(bandeau).columnGap) || 0);
    const animation = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';
    bandeau.scrollBy({ left: sens * pas, behavior: animation });
}

onMounted(() => {
    if (!liste.value) return;

    majFleches();
    observateur = new ResizeObserver(majFleches);
    observateur.observe(liste.value);
});

onBeforeUnmount(() => observateur?.disconnect());
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
            <div v-if="fiche.avis.length" class="relative -mx-5 mt-14 sm:-mx-8 md:mx-0">
                <ul
                    id="avis-google"
                    ref="liste"
                    class="flex snap-x snap-mandatory scroll-px-5 gap-4 overflow-x-auto px-5 pb-2 sm:scroll-px-8 sm:px-8 md:block md:columns-2 md:gap-5 md:overflow-visible md:px-0 md:pb-0 lg:columns-3"
                    @scroll.passive="majFleches"
                >
                    <li
                        v-for="(avis, i) in fiche.avis"
                        :key="i"
                        class="flex w-[85%] shrink-0 snap-start flex-col rounded-2xl border border-creme-300 bg-creme-50 p-7 sm:w-[55%] md:mb-5 md:w-auto md:break-inside-avoid"
                    >
                        <Etoiles :note="avis.note" class="text-lg" />
                        <p
                            v-if="avis.texte"
                            class="mt-4 text-[0.97rem] leading-relaxed whitespace-pre-line text-cacao-700"
                            :class="{ 'line-clamp-6': avis.texte.length > LONGUEUR_COUPEE && !deplies.includes(i) }"
                        >
                            {{ avis.texte }}
                        </p>
                        <p v-else class="mt-4 text-[0.97rem] text-taupe-500">Sans commentaire</p>
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

                <!-- Flèches posées sur les cartes, de chaque côté du bandeau mobile (au-delà, les avis sont
                     en colonnes) ; chacune se cache quand le bandeau est au bout de son côté. -->
                <template v-if="fiche.avis.length > 1">
                    <button
                        type="button"
                        class="absolute top-1/2 left-1.5 z-10 grid size-10 -translate-y-1/2 cursor-pointer place-items-center rounded-full border border-creme-300 bg-creme-50 text-cacao-800 shadow-[0_8px_20px_-10px_rgba(75,52,40,0.55)] transition-colors hover:border-cacao-800 hover:bg-cacao-800 hover:text-creme-50 md:hidden"
                        :class="{ invisible: !peutReculer }"
                        aria-label="Avis précédents"
                        aria-controls="avis-google"
                        @click="defiler(-1)"
                    >
                        <Icone nom="chevron-gauche" class="size-5" />
                    </button>
                    <button
                        type="button"
                        class="absolute top-1/2 right-1.5 z-10 grid size-10 -translate-y-1/2 cursor-pointer place-items-center rounded-full border border-creme-300 bg-creme-50 text-cacao-800 shadow-[0_8px_20px_-10px_rgba(75,52,40,0.55)] transition-colors hover:border-cacao-800 hover:bg-cacao-800 hover:text-creme-50 md:hidden"
                        :class="{ invisible: !peutAvancer }"
                        aria-label="Avis suivants"
                        aria-controls="avis-google"
                        @click="defiler(1)"
                    >
                        <Icone nom="chevron-droit" class="size-5" />
                    </button>
                </template>
            </div>

            <p v-if="site.googleUrl" class="mt-12 text-center">
                <!-- Texte court ; l'icône Google et le nom complet (lecteurs d'écran) disent où mène le lien. -->
                <Bouton :href="site.googleUrl" externe variante="contour" icone="google" aria-label="Voir tous les avis sur Google">Voir tous les avis</Bouton>
            </p>
            <!-- Pour les clients qui reviennent sur le site : un avis de plus sur la fiche Google. -->
            <p v-if="site.avisGoogleUrl" class="mt-6 text-center text-[0.95rem] text-cacao-700">
                Vous avez fait une séance avec moi ?
                <a
                    :href="site.avisGoogleUrl"
                    target="_blank"
                    rel="noopener"
                    class="text-cacao-800 underline decoration-or-400 underline-offset-4 transition-colors hover:text-brique"
                    >Laissez-moi un avis sur Google</a
                >
            </p>
        </div>
    </section>
</template>
