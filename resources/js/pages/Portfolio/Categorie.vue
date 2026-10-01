<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, inject } from 'vue';
import AppelReservation from '../../components/AppelReservation.vue';
import Bouton from '../../components/Bouton.vue';
import FilAriane from '../../components/FilAriane.vue';
import Galerie from '../../components/Galerie.vue';
import Seo from '../../components/Seo.vue';
import TitreSection from '../../components/TitreSection.vue';
import { useSite } from '../../composables/useSite';
import { categoriesPortfolio } from '../../data/portfolio';
import { seances } from '../../data/seances';
import { adresse, idSeance, pageJsonLd, photoJsonLd } from '../../data/seo';

const props = defineProps({
    categorie: { type: String, required: true },
    photos: { type: Array, required: true },
});

const route = inject('route');
const infos = computed(() => categoriesPortfolio[props.categorie]);
const autres = computed(() => Object.entries(categoriesPortfolio).filter(([cle]) => cle !== props.categorie));
const site = useSite();

const jsonLd = computed(() =>
    pageJsonLd(site.value, 'ImageGallery', {
        nom: infos.value.titre,
        url: adresse(site.value, route, 'portfolio.categorie', { categorie: props.categorie }),
        description: infos.value.intro,
        about: { '@id': idSeance(site.value, seances[props.categorie]) },
        ...(props.photos.length ? { image: props.photos.map((photo) => photoJsonLd(site.value, photo)) } : {}),
    }),
);
</script>

<template>
    <Seo
        :titre="`${infos.titre} – séances photo à Dijon`"
        :description="`${infos.intro} Mélanie Photographie, à Dijon et Chenôve.`"
        :json-ld="jsonLd"
    />

    <section class="pt-8 pb-20 sm:pb-28">
        <div class="conteneur">
            <FilAriane :liens="[{ libelle: 'Portfolio', href: route('portfolio') }, { libelle: infos.nom }]" />
            <TitreSection class="mt-12" balise="h1" surtitre="Portfolio" :titre="infos.nom" majuscules :manuscrit="infos.manuscrit">
                <p class="texte-courant mx-auto mt-6 max-w-2xl">{{ infos.intro }}</p>
            </TitreSection>

            <div class="mt-14">
                <Galerie :photos="photos" :vides="9" :libelle="`Photo ${infos.nom.toLowerCase()}`" />
            </div>

            <div class="mt-14 flex flex-col items-center gap-8 text-center">
                <Bouton :href="route(infos.seance)">{{ infos.libelleSeance }} en détail</Bouton>
                <p class="surtitre flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
                    Autres galeries :
                    <Link
                        v-for="[cle, autre] in autres"
                        :key="cle"
                        :href="route('portfolio.categorie', { categorie: cle })"
                        class="text-cacao-800 underline decoration-or-400 underline-offset-4 hover:text-brique"
                    >
                        {{ autre.nom }}
                    </Link>
                </p>
            </div>
        </div>
    </section>

    <AppelReservation :seance="categorie" />
</template>
