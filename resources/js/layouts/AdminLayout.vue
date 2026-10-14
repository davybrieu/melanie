<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, inject, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import Icone from '../components/Icone.vue';

// Mise en page de l'espace de gestion (pages Admin/*, qui la déclarent elles-mêmes) : ni
// l'en-tête ni le pied de page du site, et des pages jamais indexées.
const route = inject('route');
const page = usePage();

// Adresses exclues de Ziggy (config/ziggy.php), comme toutes celles de l'espace de gestion.
const rubriques = [
    { libelle: 'Bons cadeaux', href: '/admin/bons-cadeaux' },
    { libelle: 'Avis Google', href: '/admin/avis' },
];

const connecte = computed(() => page.component !== 'Admin/Connexion');
const actif = (rubrique) => page.url.split('?')[0] === rubrique.href;
const rubriqueActive = computed(() => rubriques.find(actif));

// Menu burger, sous md : il se ferme au changement de page, sur Échap et au clic à côté.
const menuOuvert = ref(false);
const boutonMenu = ref(null);

watch(() => page.url, () => (menuOuvert.value = false));

// Un lien du menu le ferme, même s'il mène à la page affichée (l'adresse ne change pas).
// L'écoute se fait sur le <nav> : un @click posé sur <Link> serait remplacé par celui d'Inertia.
const fermerSiLien = (evenement) => evenement.target.closest('a, button') && (menuOuvert.value = false);

function echap(evenement) {
    if (evenement.key === 'Escape' && menuOuvert.value) {
        menuOuvert.value = false;
        boutonMenu.value?.focus();
    }
}

onMounted(() => window.addEventListener('keydown', echap));
onBeforeUnmount(() => window.removeEventListener('keydown', echap));

const lienRubrique = 'relative py-2 text-[0.7rem] tracking-[0.2em] uppercase transition-colors';
const lienSecondaire = 'cursor-pointer text-[0.7rem] tracking-[0.2em] text-cacao-700 uppercase transition-colors hover:text-cacao-900';
</script>

<template>
    <Head>
        <meta head-key="robots" name="robots" content="noindex, nofollow" />
    </Head>

    <header class="relative z-30 border-b border-creme-300 bg-creme-50">
        <div class="conteneur flex h-16 items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-8">
                <span class="flex min-w-0 items-center gap-3">
                    <img src="/images/marque/monogramme-mp.webp" width="240" height="126" alt="Mélanie Photographie" class="h-7 w-auto" />
                    <!-- Sur mobile, la rubrique affichée (le menu burger cache les autres) -->
                    <span v-if="connecte && rubriqueActive" class="truncate text-[0.72rem] tracking-[0.24em] text-cacao-800 uppercase md:hidden">
                        {{ rubriqueActive.libelle }}
                    </span>
                </span>

                <nav v-if="connecte" aria-label="Espace de gestion" class="hidden md:block">
                    <ul class="flex items-center gap-7 whitespace-nowrap">
                        <li v-for="rubrique in rubriques" :key="rubrique.href">
                            <Link
                                :href="rubrique.href"
                                :class="[
                                    lienRubrique,
                                    actif(rubrique) ? 'text-cacao-900 after:absolute after:inset-x-0 after:-bottom-0.5 after:h-px after:bg-or-500' : 'text-cacao-700 hover:text-cacao-900',
                                ]"
                                :aria-current="actif(rubrique) ? 'page' : undefined"
                            >
                                {{ rubrique.libelle }}
                            </Link>
                        </li>
                    </ul>
                </nav>
            </div>

            <div class="items-center gap-5 whitespace-nowrap" :class="connecte ? 'hidden md:flex' : 'flex'">
                <Link :href="route('accueil')" :class="lienSecondaire">Voir le site</Link>
                <Link v-if="connecte" href="/admin/deconnexion" method="post" as="button" :class="lienSecondaire">Se déconnecter</Link>
            </div>

            <button
                v-if="connecte"
                ref="boutonMenu"
                type="button"
                class="-mr-2 grid size-11 shrink-0 cursor-pointer place-items-center rounded-full text-cacao-800 transition-colors hover:bg-creme-200 md:hidden"
                :aria-expanded="menuOuvert"
                aria-controls="menu-admin"
                :aria-label="menuOuvert ? 'Fermer le menu' : 'Ouvrir le menu'"
                @click="menuOuvert = !menuOuvert"
            >
                <Icone :nom="menuOuvert ? 'fermer' : 'menu'" class="size-6" />
            </button>
        </div>

        <!-- Menu burger (mobile) : déroulé sous l'en-tête, par-dessus la page -->
        <nav
            v-if="connecte"
            v-show="menuOuvert"
            id="menu-admin"
            aria-label="Espace de gestion"
            class="absolute inset-x-0 top-full border-b border-creme-300 bg-creme-50 shadow-[0_18px_30px_-20px_rgba(75,52,40,0.45)] md:hidden"
            @click="fermerSiLien"
        >
            <ul class="conteneur py-2">
                <li v-for="rubrique in rubriques" :key="rubrique.href">
                    <Link
                        :href="rubrique.href"
                        class="flex items-center justify-between py-3.5 font-serif text-[1.35rem] transition-colors"
                        :class="actif(rubrique) ? 'text-brique' : 'text-cacao-800 hover:text-brique'"
                        :aria-current="actif(rubrique) ? 'page' : undefined"
                    >
                        {{ rubrique.libelle }}
                        <Icone nom="chevron-droit" class="size-5 text-taupe-400" />
                    </Link>
                </li>
                <li class="mt-2 flex flex-wrap gap-x-8 gap-y-2 border-t border-creme-300 pt-4 pb-3">
                    <Link :href="route('accueil')" :class="[lienSecondaire, 'py-2']">Voir le site</Link>
                    <Link href="/admin/deconnexion" method="post" as="button" :class="[lienSecondaire, 'py-2']">Se déconnecter</Link>
                </li>
            </ul>
        </nav>
    </header>

    <!-- Voile sous le menu burger : un clic à côté du menu le ferme -->
    <div v-if="menuOuvert" class="fixed inset-0 z-20 bg-cacao-900/25 md:hidden" aria-hidden="true" @click="menuOuvert = false"></div>

    <main id="contenu" class="pb-24">
        <slot />
    </main>
</template>
