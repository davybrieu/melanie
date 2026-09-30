<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { inject, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { useSite } from '../composables/useSite';
import { navigation } from '../data/navigation';
import Bouton from './Bouton.vue';
import Fleur from './Fleur.vue';
import Icone from './Icone.vue';

const route = inject('route');
const page = usePage();
const site = useSite();

const menuOuvert = ref(false);
const defile = ref(false);

// Chemin relatif d'une route, pour marquer le lien actif (réactif à la navigation Inertia).
const chemin = (nom) => route(nom, undefined, false);
const actif = (nom) => {
    const url = page.url.split('?')[0];

    return url === chemin(nom) || url.startsWith(`${chemin(nom)}/`);
};

function basculerMenu(ouvert = !menuOuvert.value) {
    menuOuvert.value = ouvert;
    document.documentElement.classList.toggle('overflow-hidden', ouvert);
}

watch(() => page.url, () => menuOuvert.value && basculerMenu(false));

const surDefilement = () => (defile.value = window.scrollY > 12);
const echap = (evenement) => evenement.key === 'Escape' && menuOuvert.value && basculerMenu(false);

onMounted(() => {
    surDefilement();
    window.addEventListener('scroll', surDefilement, { passive: true });
    window.addEventListener('keydown', echap);
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', surDefilement);
    window.removeEventListener('keydown', echap);
});
</script>

<template>
    <header
        class="sticky top-0 z-40 border-b transition-[background-color,border-color,box-shadow] duration-300"
        :class="defile ? 'border-creme-300 bg-creme-100/92 shadow-[0_10px_30px_-24px_rgba(75,52,40,0.5)] backdrop-blur-md' : 'border-transparent bg-creme-100'"
    >
        <div class="conteneur flex h-20 max-w-7xl items-center justify-between gap-6">
            <Link :href="route('accueil')" class="flex shrink-0 items-center gap-3" aria-label="Mélanie Brieu, accueil">
                <img src="/images/marque/monogramme-mb.webp" width="480" height="253" alt="" class="h-8 w-auto sm:h-9" />
                <span class="leading-none">
                    <span class="block font-serif text-[1.02rem] tracking-[0.26em] text-cacao-800 uppercase sm:text-lg">Mélanie Brieu</span>
                    <span class="mt-1.5 block text-[0.58rem] tracking-[0.36em] text-taupe-500 uppercase">Photographe · Dijon</span>
                </span>
            </Link>

            <nav aria-label="Navigation principale" class="hidden xl:block">
                <ul class="flex items-center gap-7">
                    <li v-for="lien in navigation" :key="lien.route">
                        <Link
                            :href="route(lien.route)"
                            class="relative py-2 text-[0.72rem] tracking-[0.22em] uppercase transition-colors after:absolute after:inset-x-0 after:-bottom-0.5 after:h-px after:origin-left after:bg-or-500 after:transition-transform after:duration-300 hover:text-cacao-900"
                            :class="actif(lien.route) ? 'text-cacao-900 after:scale-x-100' : 'text-cacao-700 after:scale-x-0 hover:after:scale-x-100'"
                            :aria-current="actif(lien.route) ? 'page' : undefined"
                        >
                            {{ lien.libelle }}
                        </Link>
                    </li>
                </ul>
            </nav>

            <div class="flex items-center gap-2">
                <span class="hidden sm:block">
                    <Bouton :href="route('contact')" class="px-6! py-3!">Réserver</Bouton>
                </span>
                <button
                    type="button"
                    class="grid size-11 place-items-center rounded-full text-cacao-800 transition-colors hover:bg-creme-200 xl:hidden"
                    :aria-expanded="menuOuvert"
                    aria-controls="menu-mobile"
                    aria-label="Ouvrir le menu"
                    @click="basculerMenu(true)"
                >
                    <Icone nom="menu" class="size-6" />
                </button>
            </div>
        </div>
    </header>

    <!-- Menu mobile plein écran -->
    <div
        id="menu-mobile"
        class="fixed inset-0 z-50 flex flex-col overflow-y-auto bg-creme-100 transition-[opacity,visibility] duration-300 xl:hidden"
        :class="menuOuvert ? 'visible opacity-100' : 'invisible opacity-0'"
        role="dialog"
        aria-modal="true"
        aria-label="Menu"
    >
        <Fleur variante="gypsophile" class="absolute -top-4 -left-8 w-44 opacity-70" />
        <Fleur variante="pampa" class="absolute -right-8 -bottom-6 w-40 opacity-60" />

        <div class="conteneur flex h-20 items-center justify-end">
            <button type="button" class="grid size-11 place-items-center rounded-full text-cacao-800 hover:bg-creme-200" aria-label="Fermer le menu" @click="basculerMenu(false)">
                <Icone nom="fermer" class="size-6" />
            </button>
        </div>

        <nav aria-label="Navigation mobile" class="relative flex flex-1 flex-col items-center justify-center gap-10 px-6 pb-16">
            <img src="/images/marque/monogramme-mb.webp" width="480" height="253" alt="" class="h-12 w-auto" />
            <ul class="space-y-5 text-center">
                <li v-for="lien in navigation" :key="lien.route">
                    <Link
                        :href="route(lien.route)"
                        class="font-serif text-[1.7rem] tracking-[0.12em] uppercase transition-colors"
                        :class="actif(lien.route) ? 'text-brique' : 'text-cacao-800 hover:text-brique'"
                    >
                        {{ lien.libelle }}
                    </Link>
                </li>
            </ul>
            <Bouton :href="route('contact')">Réserver ma séance</Bouton>
            <a :href="site.instagramUrl" target="_blank" rel="noopener" class="inline-flex items-center gap-2 text-sm text-taupe-600 hover:text-cacao-800">
                <Icone nom="instagram" class="size-5" /> @{{ site.instagram }}
            </a>
        </nav>
    </div>
</template>
