<script setup>
import { computed, inject } from 'vue';
import AppelReservation from '../components/AppelReservation.vue';
import Bouton from '../components/Bouton.vue';
import CarteTarif from '../components/CarteTarif.vue';
import EnTetePage from '../components/EnTetePage.vue';
import Etapes from '../components/Etapes.vue';
import Fleur from '../components/Fleur.vue';
import Galerie from '../components/Galerie.vue';
import Icone from '../components/Icone.vue';
import Photo from '../components/Photo.vue';
import SectionFaq from '../components/SectionFaq.vue';
import Seo from '../components/Seo.vue';
import TitreSection from '../components/TitreSection.vue';
import { useSite } from '../composables/useSite';
import { communesCitees, conditions } from '../data/conditions';
import { faqNouveauNe } from '../data/faq';
import { parcours } from '../data/parcours';
import { euros, photosIncluses, seances } from '../data/seances';
import { serviceJsonLd } from '../data/seo';

const props = defineProps({
    photos: { type: Object, required: true },
});

const route = inject('route');
const site = useSite();
const seance = seances['nouveau-ne'];

// « Naissance » et « bébé » : les mots que les parents tapent aussi souvent que « nouveau-né ».
const description = `Photographe naissance et nouveau-né à Dijon, formée et vaccinée contre la coqueluche : séance bébé à domicile, sa sécurité avant tout. Séance à ${euros(seance.prix)}.`;

// Pas de FAQPage ici : les questions sont balisées une seule fois, sur la page FAQ.
const jsonLd = computed(() => serviceJsonLd(site.value, route, seance));

const grandes = computed(() => [props.photos.grandes[0] ?? null, props.photos.grandes[1] ?? null]);

// Ce qui permet à Mélanie de prendre soin de bébé : faits confirmés par elle (data/parcours.js).
const atouts = [
    {
        icone: 'diplome',
        titre: 'Formée au nouveau-né',
        texte: `Certificat du cours en ligne «\u00a0${parcours.formation.nom}\u00a0» de ${parcours.formation.studio} (${parcours.formation.plateforme}), avec tout un volet sur la sécurité de bébé.`,
    },
    { icone: 'vaccin', titre: 'Vaccinée contre la coqueluche', texte: 'Une protection recommandée pour toutes les personnes au contact des nourrissons.' },
    { icone: 'coeurs', titre: `Maman de ${parcours.enfants} enfants`, texte: 'Porter, apaiser, emmailloter un tout-petit : des gestes que je connais par cœur.' },
    { icone: 'sante', titre: 'Je travaille dans la santé', texte: 'En parallèle de la photo, j’exerce dans le milieu de la santé, où l’hygiène et la vigilance sont la règle.' },
];

// Engagements sécurité et bien-être pendant la séance.
const engagements = [
    { icone: 'horloge', titre: 'Bébé mène la danse', texte: `La séance suit son rythme : tétées, câlins, changes. Rien n’est jamais forcé, c’est pour cela que je prévois ${seance.duree}.` },
    { icone: 'main', titre: 'Des positions naturelles', texte: 'Je ne propose que des positions confortables et naturelles pour un nouveau-né. Si bébé n’est pas à l’aise, on n’insiste pas.' },
    { icone: 'bouclier', titre: 'Toujours une main près de lui', texte: 'Bébé n’est jamais laissé seul, ni en hauteur sans surveillance : un parent ou moi restons toujours à portée de main.' },
    { icone: 'soleil', titre: 'Au chaud, en douceur', texte: 'Une pièce bien chauffée, une lumière naturelle et douce, des gestes lents et une voix calme.' },
    { icone: 'check', titre: 'Une hygiène rigoureuse', texte: 'Mains lavées et désinfectées, tenues et tissus propres pour chaque séance, pas de parfum.' },
    { icone: 'coeur', titre: 'Vous restez à ses côtés', texte: 'Vous êtes présents pendant toute la séance. Une question, un doute ? On en parle, à tout moment.' },
];

const etapes = [
    { titre: 'Pendant la grossesse', texte: 'Vous réservez : je bloque une période autour de votre terme.' },
    { titre: 'À la naissance', texte: 'Un petit message pour m’annoncer l’arrivée de bébé, et nous fixons la date, idéalement entre 5 et 15 jours.' },
    { titre: 'Le jour J', texte: `Je viens chez vous : ${seance.duree} tout en douceur, à son rythme, avec pauses tétées et câlins.` },
    { titre: 'Vos souvenirs', texte: `Nous choisissons ensemble vos ${photosIncluses} photos préférées, que je retouche et vous livre en haute définition ${conditions.livraison}.` },
];

const moments = ['Bébé paisiblement endormi', 'Blotti dans vos bras', 'Ses minuscules mains et petits pieds', 'Les premiers regards', 'La rencontre avec les aînés', 'Votre nouvelle famille, au complet'];
</script>

<template>
    <Seo titre="Photographe naissance et nouveau-né à Dijon" :description="description" :json-ld="jsonLd" />

    <EnTetePage
        :fil="[{ libelle: 'Nouveau-né' }]"
        surtitre="Séance photo naissance à domicile"
        titre="Photographe naissance et nouveau-né à Dijon"
        :manuscrit="seance.accroche"
        :photo="photos.hero"
        libelle-photo="Photo nouveau-né principale"
    >
        <p class="mt-7 font-serif text-[1.45rem] leading-snug text-cacao-800 sm:text-[1.7rem]">
            Les premiers jours ne durent pas.<br />Les photographies, oui.
        </p>
        <p class="texte-courant mx-auto mt-5 max-w-xl lg:mx-0">
            Ses petits doigts qui s’enroulent autour des vôtres, son odeur, ses mimiques pendant son sommeil… Tout change si vite. Je viens chez vous, à Dijon ou dans ses alentours, pour une séance photo de naissance tout en douceur, pensée d’abord pour le confort et la sécurité de bébé.
        </p>
        <div class="mt-9 flex flex-wrap justify-center gap-4 lg:justify-start">
            <Bouton :href="route('contact', { seance: 'nouveau-ne' })">Réserver pendant la grossesse</Bouton>
        </div>
    </EnTetePage>

    <!-- Sécurité et bien-être : juste après l'en-tête -->
    <section id="securite" class="relative isolate overflow-hidden border-y border-creme-300 bg-creme-50 py-20 sm:py-28">
        <Fleur variante="gypsophile-coeur" differee class="absolute -right-6 -bottom-6 -z-10 w-36 opacity-60 sm:w-48" />
        <div class="conteneur">
            <TitreSection surtitre="Sécurité & bien-être" titre="Le bien-être de bébé passe avant tout" manuscrit="Douceur, bienveillance & patience" />
            <p class="texte-courant mx-auto mt-8 max-w-2xl text-center">
                Un nouveau-né est fragile, et vous me confiez ce que vous avez de plus précieux. Chaque séance repose sur des règles simples, que je ne négocie jamais : la sécurité de bébé compte plus que n’importe quelle photo.
            </p>
            <!-- Formation, vaccination, famille, métier : rien d'autre ne doit être revendiqué (CLAUDE.md, Règles de contenu) -->
            <ul class="mt-12 grid gap-x-6 gap-y-8 rounded-2xl bg-poudre-100 px-6 py-9 sm:grid-cols-2 sm:px-10 lg:grid-cols-4">
                <li v-for="atout in atouts" :key="atout.titre" class="text-center">
                    <span class="mx-auto grid size-14 place-items-center rounded-full bg-creme-50 text-cacao-800">
                        <Icone :nom="atout.icone" class="size-7" />
                    </span>
                    <h3 class="mt-4 text-lg leading-snug">{{ atout.titre }}</h3>
                    <p class="mt-2 text-[0.92rem] leading-relaxed text-cacao-700">{{ atout.texte }}</p>
                </li>
            </ul>
            <ul class="mt-6 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                <li v-for="engagement in engagements" :key="engagement.titre" class="rounded-2xl border border-creme-300 bg-creme-100 p-7">
                    <span class="grid size-14 place-items-center rounded-full bg-poudre-100 text-cacao-800">
                        <Icone :nom="engagement.icone" class="size-7" />
                    </span>
                    <h3 class="mt-5 text-xl">{{ engagement.titre }}</h3>
                    <p class="mt-2 text-[0.97rem] leading-relaxed text-cacao-700">{{ engagement.texte }}</p>
                </li>
            </ul>
        </div>
    </section>

    <!-- Déroulement -->
    <section class="py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Déroulement" titre="Une séance photo naissance à domicile, dans votre cocon" />
            <p class="texte-courant mx-auto mt-8 max-w-2xl text-center">
                Pas de trajet avec un nourrisson de quelques jours : je me déplace chez vous, à Dijon et dans les communes alentour ({{ communesCitees }}). Bébé reste dans son environnement, avec ses odeurs et ses repères, et vous restez dans votre bulle.
            </p>
            <div class="mt-12">
                <Etapes :etapes="etapes" />
            </div>
        </div>
    </section>

    <!-- Ce que je photographie + grandes photos -->
    <section class="bg-creme-200/60 py-20 sm:py-28">
        <div class="conteneur grid items-center gap-14 lg:grid-cols-2 lg:gap-20">
            <div class="grid grid-cols-2 gap-4">
                <Photo :photo="grandes[0]" libelle="Grande photo nouveau-né" sizes="(min-width: 1024px) 25vw, 50vw" class="aspect-[3/4] rounded-t-full" />
                <Photo :photo="grandes[1]" libelle="Grande photo nouveau-né" sizes="(min-width: 1024px) 25vw, 50vw" class="mt-12 aspect-[3/4] rounded-b-full" />
            </div>
            <div class="text-center lg:text-left">
                <TitreSection surtitre="Vos souvenirs" titre="Ce que nous allons garder pour toujours" :centre="false" class="text-center lg:text-left" />
                <ul class="mt-8 space-y-3 text-left">
                    <li v-for="moment in moments" :key="moment" class="flex items-center gap-4 border-b border-creme-300 pb-3 text-[1.02rem] text-cacao-700">
                        <Icone nom="coeur" class="size-4 shrink-0 fill-poudre-300 text-poudre-400" />
                        {{ moment }}
                    </li>
                </ul>
            </div>
        </div>
    </section>

    <!-- Tarif -->
    <section id="tarif" class="py-20 sm:py-28">
        <div class="conteneur max-w-4xl">
            <TitreSection surtitre="Mes offres" coeurs titre="Tarif de la séance nouveau-né" />
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
            <TitreSection surtitre="Galerie" titre="Quelques séances naissance et nouveau-né" manuscrit="Des premiers instants précieux" />
            <div class="mt-12">
                <Galerie :photos="photos.galerie" libelle="Photo nouveau-né" />
            </div>
            <p class="mt-10 text-center">
                <Bouton :href="route('portfolio.categorie', { categorie: 'nouveau-ne' })" variante="contour">Voir le portfolio nouveau-né</Bouton>
            </p>
        </div>
    </section>

    <SectionFaq titre="Vos questions sur la séance nouveau-né" :questions="faqNouveauNe" ancre="nouveau-ne" />

    <AppelReservation
        seance="nouveau-ne"
        titre="Réservez pendant la grossesse"
        texte="La période idéale est courte : réservez dès maintenant, nous fixerons la date exacte après la naissance de bébé."
    />
</template>
