<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, inject } from 'vue';
import Icone from '../components/Icone.vue';
import SiteFooter from '../components/SiteFooter.vue';
import SiteHeader from '../components/SiteHeader.vue';

const route = inject('route');
const page = usePage();

// Bouton « Réserver » fixé en bas de l'écran sur mobile, sauf sur la page contact (le formulaire y est déjà).
const reserverMobile = computed(() => !page.url.startsWith(route('contact', undefined, false)));
</script>

<template>
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-full focus:bg-cacao-800 focus:px-5 focus:py-3 focus:text-sm focus:text-creme-50">
        Aller au contenu
    </a>
    <SiteHeader />
    <main id="contenu">
        <slot />
    </main>
    <SiteFooter />

    <template v-if="reserverMobile">
        <!-- Réserve la hauteur du bouton pour qu'il ne cache pas le bas du pied de page -->
        <div class="h-[calc(3.5rem+env(safe-area-inset-bottom))] bg-creme-50 sm:hidden" aria-hidden="true"></div>
        <Link
            :href="route('contact')"
            class="fixed inset-x-0 bottom-0 z-40 flex items-center justify-center gap-2.5 bg-cacao-800 pt-4 pb-[calc(1rem+env(safe-area-inset-bottom))] text-[0.78rem] font-normal tracking-[0.24em] text-creme-50 uppercase shadow-[0_-12px_30px_-18px_rgba(46,33,26,0.55)] transition-colors hover:bg-brique sm:hidden"
        >
            <Icone nom="calendrier" class="size-4" />
            Réserver une séance
        </Link>
    </template>
</template>
