<script setup>
import { Link } from '@inertiajs/vue3';
import { inject } from 'vue';
import AppelReservation from '../../components/AppelReservation.vue';
import FilAriane from '../../components/FilAriane.vue';
import Icone from '../../components/Icone.vue';
import Photo from '../../components/Photo.vue';
import Seo from '../../components/Seo.vue';
import TitreSection from '../../components/TitreSection.vue';
import { categoriesPortfolio } from '../../data/portfolio';

defineProps({
    couvertures: { type: Object, required: true },
});

const route = inject('route');
</script>

<template>
    <Seo
        titre="Portfolio – photographe grossesse, nouveau-né & famille"
        description="Découvrez le portfolio de Mélanie Photographie à Dijon : séances grossesse, nouveau-né et famille, des images douces, naturelles et lumineuses."
    />

    <section class="pt-8 pb-20 sm:pb-28">
        <div class="conteneur">
            <FilAriane :liens="[{ libelle: 'Portfolio' }]" />
            <TitreSection class="mt-12" balise="h1" surtitre="Mon travail" titre="Portfolio" majuscules manuscrit="Des souvenirs vrais, remplis d’émotions">
                <p class="texte-courant mx-auto mt-6 max-w-2xl">
                    Trois univers, une même envie : des images douces et naturelles, où l’on se reconnaît. Choisissez celui qui vous parle.
                </p>
            </TitreSection>

            <div class="mt-16 grid gap-14 md:grid-cols-3 md:gap-8 lg:gap-12">
                <Link
                    v-for="(categorie, cle) in categoriesPortfolio"
                    :key="cle"
                    :href="route('portfolio.categorie', { categorie: cle })"
                    class="group block text-center"
                >
                    <Photo
                        :photo="couvertures[cle]"
                        :libelle="`Couverture ${categorie.nom.toLowerCase()}`"
                        sizes="(min-width: 768px) 30vw, 100vw"
                        class="mx-auto aspect-[4/5] max-w-sm rounded-t-full"
                    />
                    <h2 class="mt-7 text-2xl tracking-[0.14em] uppercase">{{ categorie.nom }}</h2>
                    <p class="manuscrit mt-1 text-[2.1rem]">{{ categorie.manuscrit }}</p>
                    <span class="mt-4 inline-flex items-center gap-2 text-[0.7rem] tracking-[0.24em] text-cacao-800 uppercase">
                        Voir la galerie
                        <Icone nom="fleche" class="size-4 transition-transform duration-300 group-hover:translate-x-1" />
                    </span>
                </Link>
            </div>
        </div>
    </section>

    <AppelReservation />
</template>
