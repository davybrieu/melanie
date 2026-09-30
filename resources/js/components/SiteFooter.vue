<script setup>
import { Link } from '@inertiajs/vue3';
import { inject } from 'vue';
import { useSite } from '../composables/useSite';
import Fleur from './Fleur.vue';
import Icone from './Icone.vue';

const route = inject('route');
const site = useSite();
const annee = new Date().getFullYear();

const colonnes = [
    {
        titre: 'Séances',
        liens: [
            { libelle: 'Photographe grossesse', route: 'grossesse' },
            { libelle: 'Photographe nouveau-né', route: 'nouveau-ne' },
            { libelle: 'Photographe famille', route: 'famille' },
            { libelle: 'Tarifs', route: 'tarifs' },
            { libelle: 'Bon cadeau', route: 'bon-cadeau' },
        ],
    },
    {
        titre: 'Découvrir',
        liens: [
            { libelle: 'Portfolio', route: 'portfolio' },
            { libelle: 'Mon univers', route: 'a-propos' },
            { libelle: 'Questions fréquentes', route: 'faq' },
            { libelle: 'Contact', route: 'contact' },
        ],
    },
];
</script>

<template>
    <footer class="relative isolate overflow-hidden border-t border-creme-300 bg-creme-50">
        <Fleur variante="gypsophile-coeur" class="absolute right-4 bottom-16 -z-10 w-32 opacity-60 sm:w-44" />

        <div class="conteneur py-16 sm:py-20">
            <div class="grid gap-12 lg:grid-cols-[1.3fr_1fr_1fr_1.2fr]">
                <div class="text-center lg:text-left">
                    <Link :href="route('accueil')" class="inline-block" aria-label="MB Photographie, accueil">
                        <img src="/images/marque/logo-mb-photographie.webp" width="624" height="405" alt="MB Photographie" loading="lazy" class="w-52" />
                    </Link>
                    <p class="surtitre mx-auto mt-5 max-w-60 text-[0.62rem]! text-balance lg:mx-0">Capturer les plus beaux moments de vie</p>
                </div>

                <nav v-for="colonne in colonnes" :key="colonne.titre" :aria-label="colonne.titre" class="text-center lg:text-left">
                    <p class="surtitre">{{ colonne.titre }}</p>
                    <ul class="mt-5 space-y-3">
                        <li v-for="lien in colonne.liens" :key="lien.route">
                            <Link :href="route(lien.route)" class="text-[0.95rem] text-cacao-700 transition-colors hover:text-brique">{{ lien.libelle }}</Link>
                        </li>
                    </ul>
                </nav>

                <div class="text-center lg:text-left">
                    <p class="surtitre">Me trouver</p>
                    <ul class="mt-5 space-y-3 text-[0.95rem]">
                        <li class="inline-flex items-start gap-2.5">
                            <Icone nom="localisation" class="mt-0.5 size-5 shrink-0 text-or-500" />
                            <span>{{ site.commune }}, près de {{ site.ville }}<br /><span class="text-taupe-500">Déplacements sur Dijon et alentours</span></span>
                        </li>
                        <li>
                            <a :href="site.instagramUrl" target="_blank" rel="noopener" class="inline-flex items-center gap-2.5 transition-colors hover:text-brique">
                                <Icone nom="instagram" class="size-5 text-or-500" /> @{{ site.instagram }}
                            </a>
                        </li>
                        <li>
                            <a :href="`mailto:${site.email}`" class="inline-flex items-center gap-2.5 break-all transition-colors hover:text-brique">
                                <Icone nom="email" class="size-5 shrink-0 text-or-500" /> {{ site.email }}
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="border-t border-creme-300">
            <div class="conteneur flex flex-col items-center justify-between gap-3 py-6 text-xs tracking-wide text-taupe-500 sm:flex-row">
                <p>© {{ annee }} {{ site.nom }} · {{ site.marque }}</p>
                <ul class="flex flex-wrap justify-center gap-x-5 gap-y-1">
                    <li><Link :href="route('mentions-legales')" class="hover:text-cacao-800">Mentions légales</Link></li>
                    <li><Link :href="route('confidentialite')" class="hover:text-cacao-800">Confidentialité</Link></li>
                    <li><Link :href="route('cgv')" class="hover:text-cacao-800">CGV</Link></li>
                </ul>
            </div>
        </div>
    </footer>
</template>
