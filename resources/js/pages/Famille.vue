<script setup>
import { computed, inject } from 'vue';
import AppelReservation from '../components/AppelReservation.vue';
import Bouton from '../components/Bouton.vue';
import CarteTarif from '../components/CarteTarif.vue';
import EnTetePage from '../components/EnTetePage.vue';
import Galerie from '../components/Galerie.vue';
import Icone from '../components/Icone.vue';
import Photo from '../components/Photo.vue';
import SectionFaq from '../components/SectionFaq.vue';
import Seo from '../components/Seo.vue';
import TitreSection from '../components/TitreSection.vue';
import { useSite } from '../composables/useSite';
import { faqFamille } from '../data/faq';
import { euros, seances } from '../data/seances';
import { serviceJsonLd } from '../data/seo';

const props = defineProps({
    photos: { type: Object, required: true },
});

const route = inject('route');
const site = useSite();
const seance = seances.famille;

const description = `Photographe famille à Dijon et alentours : une séance naturelle où les enfants restent des enfants, en extérieur ou à la maison. Séance à ${euros(seance.prix)}.`;

// Pas de FAQPage ici : les questions sont balisées une seule fois, sur la page FAQ.
const jsonLd = computed(() => serviceJsonLd(site.value, route, seance));

const grandes = computed(() => [props.photos.grandes[0] ?? null, props.photos.grandes[1] ?? null]);

const moments = [
    { icone: 'famille', titre: 'La famille au complet', texte: 'Tout le monde réuni, pour la photo que l’on encadre dans le salon.' },
    { icone: 'coeurs', titre: 'Parents & enfants', texte: 'Maman et sa fille, papa et son fils, les complicités de chacun.' },
    { icone: 'brin', titre: 'La fratrie', texte: 'Les grands qui veillent sur les petits, les chamailleries et les fous rires.' },
    { icone: 'appareil', titre: 'Des portraits de chacun', texte: 'Un joli portrait de chaque membre de la famille, parents compris.' },
    { icone: 'soleil', titre: 'Des jeux', texte: 'Courses, chatouilles, envols dans les bras : les jeux font naître les vrais sourires.' },
    { icone: 'coeur', titre: 'Des câlins', texte: 'Les bras qui se serrent, les bisous dans le cou, la tendresse du quotidien.' },
    { icone: 'image', titre: 'Des moments spontanés', texte: 'Ceux que l’on ne prévoit pas, et que l’on regarde avec le plus d’émotion.' },
];
</script>

<template>
    <Seo titre="Photographe famille à Dijon" :description="description" :json-ld="jsonLd" />

    <EnTetePage
        :fil="[{ libelle: 'Famille' }]"
        surtitre="Séance photo famille à Dijon"
        titre="Photographe famille à Dijon"
        :manuscrit="seance.accroche"
        :photo="photos.hero"
        libelle-photo="Photo famille principale"
    >
        <p class="texte-courant mx-auto mt-7 max-w-xl lg:mx-0">
            Les enfants grandissent à toute vitesse, et les photos de famille sont souvent celles où il manque quelqu’un : vous, derrière l’appareil. Offrez-vous un vrai moment ensemble, à Dijon ou dans ses alentours, et laissez-moi garder la trace de votre famille telle qu’elle est aujourd’hui.
        </p>
        <div class="mt-9 flex flex-wrap justify-center gap-4 lg:justify-start">
            <Bouton :href="route('contact', { seance: 'famille' })">Réserver ma séance</Bouton>
        </div>
    </EnTetePage>

    <!-- L'expérience -->
    <section class="bg-creme-50 py-20 sm:py-28">
        <div class="conteneur grid items-center gap-14 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.1fr)] lg:gap-20">
            <div class="text-center lg:text-left">
                <TitreSection surtitre="L’expérience" titre="Une séance où les enfants ont le droit d’être des enfants" :centre="false" />
                <div class="texte-courant mt-8 space-y-4">
                    <p>Oubliez les « Souris ! » et les rangs d’oignons. Pendant une séance famille, personne n’a besoin de rester sage : les enfants courent, sautent dans les flaques, font les pitres… et c’est exactement ce que j’ai envie de photographier.</p>
                    <p>Nous commençons par quelques photos tous ensemble, le temps que chacun prenne ses marques. Puis je vous propose des petits jeux et des moments complices : une course, une histoire chuchotée, un câlin géant. Je vous guide sans rien figer, pour que les émotions restent vraies.</p>
                    <p>Et vous, les parents ? Vous profitez. Pas besoin de gérer la photo : je m’occupe de tout, vous n’avez qu’à être ensemble.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <Photo :photo="grandes[0]" libelle="Grande photo famille" sizes="(min-width: 1024px) 25vw, 50vw" class="aspect-[3/4] rounded-2xl" />
                <Photo :photo="grandes[1]" libelle="Grande photo famille" sizes="(min-width: 1024px) 25vw, 50vw" class="mt-12 aspect-[3/4] rounded-2xl" />
            </div>
        </div>
    </section>

    <!-- Ce que nous allons photographier -->
    <section class="py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Au programme" titre="Ce que nous allons photographier" manuscrit="Votre tribu, votre histoire" />
            <ul class="mt-14 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                <li
                    v-for="(moment, i) in moments"
                    :key="moment.titre"
                    class="rounded-2xl border border-creme-300 bg-creme-50 p-7"
                    :class="i === moments.length - 1 ? 'sm:col-span-2 lg:col-span-1' : ''"
                >
                    <span class="grid size-12 place-items-center rounded-full bg-poudre-100 text-cacao-800">
                        <Icone :nom="moment.icone" class="size-6" />
                    </span>
                    <h3 class="mt-5 text-xl">{{ moment.titre }}</h3>
                    <p class="mt-2 text-[0.95rem] leading-relaxed text-cacao-700">{{ moment.texte }}</p>
                </li>
            </ul>
        </div>
    </section>

    <!-- Où -->
    <section class="border-y border-creme-300 bg-creme-200/60 py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Où ?" titre="En extérieur ou à la maison" />
            <div class="mt-12 grid gap-5 md:grid-cols-2">
                <article class="rounded-2xl bg-creme-50 p-8 sm:p-10">
                    <Icone nom="soleil" class="size-9 text-or-500" />
                    <h3 class="mt-5 text-2xl">En extérieur</h3>
                    <p class="texte-courant mt-3">
                        Un parc, un champ, une forêt, un bord d’eau autour de Dijon : nous choisissons ensemble un lieu qui vous ressemble. La lumière douce de fin de journée donne des images chaleureuses, et les enfants ont de l’espace pour se défouler.
                    </p>
                </article>
                <article class="rounded-2xl bg-creme-50 p-8 sm:p-10">
                    <Icone nom="maison" class="size-9 text-or-500" />
                    <h3 class="mt-5 text-2xl">À la maison</h3>
                    <p class="texte-courant mt-3">
                        Chez vous, dans votre cocon : batailles de coussins, histoires sur le lit, petit-déjeuner en pyjama… Des images intimes et pleines de vie, idéales avec un bébé ou quand la météo n’est pas de la partie.
                    </p>
                </article>
            </div>
        </div>
    </section>

    <!-- Tarif -->
    <section id="tarif" class="py-20 sm:py-28">
        <div class="conteneur max-w-4xl">
            <TitreSection surtitre="Mes offres" coeurs titre="Tarif de la séance famille" />
            <div class="mt-12">
                <CarteTarif :offre="seance" />
            </div>
            <p class="mt-8 text-center">
                <Bouton :href="route('tarifs')" variante="lien">Tous les tarifs</Bouton>
            </p>
        </div>
    </section>

    <!-- Galerie -->
    <section class="bg-creme-50 py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Galerie" titre="Quelques séances famille" manuscrit="Des souvenirs qui vous ressemblent" />
            <div class="mt-12">
                <Galerie :photos="photos.galerie" libelle="Photo famille" />
            </div>
            <p class="mt-10 text-center">
                <Bouton :href="route('portfolio.categorie', { categorie: 'famille' })" variante="contour">Voir le portfolio famille</Bouton>
            </p>
        </div>
    </section>

    <SectionFaq titre="Vos questions sur la séance famille" :questions="faqFamille" ancre="famille" />

    <AppelReservation seance="famille" titre="Envie de photos qui vous ressemblent ?" />
</template>
