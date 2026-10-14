<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { inject } from 'vue';

// Mise en page de l'espace de gestion (pages Admin/*, qui la déclarent elles-mêmes) : ni
// l'en-tête ni le pied de page du site, et des pages jamais indexées.
const route = inject('route');
const page = usePage();
</script>

<template>
    <Head>
        <meta head-key="robots" name="robots" content="noindex, nofollow" />
    </Head>

    <header class="border-b border-creme-300 bg-creme-50">
        <div class="conteneur flex h-16 items-center justify-between gap-4">
            <span class="flex items-center gap-3">
                <img src="/images/marque/monogramme-mp.webp" width="240" height="126" alt="Mélanie Photographie" class="h-7 w-auto" />
                <span class="hidden text-[0.72rem] tracking-[0.24em] text-cacao-800 uppercase sm:inline">Bons cadeaux</span>
            </span>
            <nav aria-label="Espace de gestion" class="flex items-center gap-5 text-[0.7rem] tracking-[0.2em] whitespace-nowrap uppercase">
                <Link :href="route('accueil')" class="text-cacao-700 transition-colors hover:text-cacao-900">Voir le site</Link>
                <Link
                    v-if="page.component !== 'Admin/Connexion'"
                    href="/admin/deconnexion"
                    method="post"
                    as="button"
                    class="cursor-pointer tracking-[0.2em] text-cacao-700 uppercase transition-colors hover:text-cacao-900"
                >
                    Se déconnecter
                </Link>
            </nav>
        </div>
    </header>

    <main id="contenu" class="pb-24">
        <slot />
    </main>
</template>
