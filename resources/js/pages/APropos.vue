<script setup>
import { computed, inject } from 'vue';
import AppelReservation from '../components/AppelReservation.vue';
import Bouton from '../components/Bouton.vue';
import FilAriane from '../components/FilAriane.vue';
import Fleur from '../components/Fleur.vue';
import Icone from '../components/Icone.vue';
import Photo from '../components/Photo.vue';
import Pinceau from '../components/Pinceau.vue';
import Seo from '../components/Seo.vue';
import TitreSection from '../components/TitreSection.vue';
import { useSite } from '../composables/useSite';
import { entrepriseJsonLd } from '../data/seo';

defineProps({
    photos: { type: Object, required: true },
});

const route = inject('route');
const site = useSite();

const jsonLd = computed(() => [
    entrepriseJsonLd(site.value),
    { '@type': 'AboutPage', name: 'Mon univers', url: route('a-propos'), about: { '@id': `${site.value.url}/#entreprise` } },
]);

// Textes à la première personne. À relire et personnaliser par Mélanie :
// aucun fait biographique n'a été inventé (formations, parcours, enfants…).
const chapitres = [
    {
        titre: 'Mon histoire',
        paragraphes: [
            'J’ai créé MB Photographie avec une envie toute simple : garder une trace des moments qui passent trop vite. Ces instants où tout change, où une famille s’agrandit, où l’on devient parent, où les enfants grandissent sans prévenir.',
            // À personnaliser : comment la photographie est entrée dans ta vie, ce qui t'a décidée à te lancer.
        ],
    },
    {
        titre: 'Pourquoi la photographie',
        paragraphes: [
            'Parce qu’une photo a ce pouvoir incroyable d’arrêter le temps. Des années plus tard, elle fait revivre une chaleur, une odeur, un fou rire. J’aime l’idée que mes images soient regardées, encadrées, transmises, et qu’elles racontent votre histoire à ceux qui viendront après.',
        ],
    },
    {
        titre: 'Pourquoi la grossesse, les nouveau-nés et les familles',
        paragraphes: [
            'Parce que ce sont des moments éphémères. Un ventre n’est rond que quelques semaines, un nouveau-né change chaque jour, les enfants grandissent à toute vitesse. Ce sont aussi des moments chargés d’amour, et c’est précisément cet amour que j’aime photographier.',
        ],
    },
];

const facettes = [
    {
        icone: 'coeur',
        titre: 'Ma personnalité',
        texte: 'Douce, patiente et à l’écoute. Je prends le temps de discuter, de rassurer, de rire aussi. Une séance avec moi, c’est d’abord une rencontre.',
    },
    {
        icone: 'appareil',
        titre: 'Ma manière de travailler',
        texte: 'Je vous guide sans jamais figer les choses. Quelques indications simples, beaucoup de liberté, et une attention constante aux petits gestes qui en disent long.',
    },
    {
        icone: 'coeurs',
        titre: 'Ce que je veux vous faire ressentir',
        texte: 'Que vous vous sentiez à l’aise et écoutés pendant la séance. Et plus tard, en découvrant vos photos, que l’émotion du jour J revienne intacte.',
    },
    {
        icone: 'famille',
        titre: 'Mon rapport aux familles',
        texte: 'Chaque famille est unique : recomposée, nombreuse, à deux ou à quatre pattes. Je n’ai pas de modèle idéal en tête, je photographie la vôtre telle qu’elle est aujourd’hui.',
    },
    {
        icone: 'soleil',
        titre: 'Mon approche des enfants',
        texte: 'Avec les enfants, je joue. Je me mets à leur hauteur, je leur propose des jeux, je les laisse courir. Un enfant qui s’amuse oublie l’appareil photo : c’est là que naissent les images les plus vraies.',
    },
    {
        icone: 'grossesse',
        titre: 'Mon rapport à la maternité',
        texte: 'La maternité mérite d’être célébrée avec douceur et sans jugement. Chaque femme vit sa grossesse et ses premiers jours de maman à sa façon ; je veux que chacune se trouve belle sur ses photos.',
    },
];

const raisons = [
    { icone: 'coeur', titre: 'Douceur, bienveillance & patience', texte: 'Pas de stress, pas de pression : on avance à votre rythme, et à celui de bébé.' },
    { icone: 'image', titre: 'Des images naturelles & authentiques', texte: 'Une lumière douce, des tons chauds, des émotions vraies. Vous restez vous.' },
    { icone: 'bouclier', titre: 'La sécurité de bébé avant tout', texte: 'Des positions naturelles et une vigilance de chaque instant pendant les séances nouveau-né.' },
    { icone: 'brin', titre: 'Une expérience simple & agréable', texte: 'Des conseils pour se préparer, le prêt de tenues inclus, et un vrai moment à partager.' },
    { icone: 'coeurs', titre: 'Des souvenirs qui vous ressemblent', texte: 'Chaque séance est pensée avec vous, selon vos envies et votre histoire.' },
    { icone: 'localisation', titre: 'Tout près de chez vous', texte: 'Installée à Chenôve, je me déplace à Dijon et dans ses alentours.' },
];
</script>

<template>
    <Seo
        titre="Mon univers – Mélanie, photographe à Dijon"
        description="Bonjour, moi c’est Mélanie, photographe grossesse, nouveau-né et famille à Chenôve et Dijon. Découvrez mon histoire, ma façon de travailler et ce qui me tient à cœur."
        :json-ld="jsonLd"
    />

    <!-- Grande photo + présentation -->
    <section class="relative isolate overflow-hidden pt-8 pb-20 sm:pb-28">
        <Fleur variante="pampa" class="absolute top-24 -right-12 -z-10 hidden w-56 opacity-70 lg:block" />
        <div class="conteneur">
            <FilAriane :liens="[{ libelle: 'Mon univers' }]" />
            <div class="mt-10 grid items-center gap-12 lg:mt-14 lg:grid-cols-2 lg:gap-20">
                <Photo :photo="photos.portrait" prioritaire libelle="Grand portrait de Mélanie" sizes="(min-width: 1024px) 45vw, 100vw" class="mx-auto aspect-[4/5] w-full max-w-lg rounded-t-full" />
                <div class="text-center lg:text-left">
                    <p class="surtitre">Mon univers</p>
                    <h1 class="mt-3 text-[2.5rem] leading-[1.1] sm:text-[3.4rem]">Bonjour, moi c’est Mélanie.</h1>
                    <p class="mt-4">
                        <span class="manuscrit relative isolate inline-block px-7 pt-3 pb-1 text-[2.3rem] sm:text-[2.6rem]">
                            <Pinceau class="text-poudre-200" />
                            Enchantée !
                        </span>
                    </p>
                    <div class="texte-courant mt-7 space-y-4">
                        <p>
                            J’ai {{ site.age }} ans, je vis à Chenôve, à deux pas de Dijon, et je suis photographe de la grossesse, des nouveau-nés et des familles.
                        </p>
                        <p>
                            Avant de me confier vos souvenirs, et parfois de m’ouvrir la porte de votre maison, vous avez le droit de savoir qui je suis. Alors voilà un peu de moi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Histoire -->
    <section class="bg-creme-50 py-20 sm:py-28">
        <div class="conteneur grid gap-14 lg:grid-cols-[minmax(0,1.2fr)_minmax(0,1fr)] lg:gap-20">
            <div class="space-y-12">
                <article v-for="chapitre in chapitres" :key="chapitre.titre">
                    <h2 class="text-[1.9rem] leading-tight sm:text-4xl">{{ chapitre.titre }}</h2>
                    <p v-for="(paragraphe, i) in chapitre.paragraphes.filter(Boolean)" :key="i" class="texte-courant mt-4">{{ paragraphe }}</p>
                </article>
            </div>
            <div class="relative">
                <Photo :photo="photos.ambiance" libelle="Photo d’ambiance" sizes="(min-width: 1024px) 40vw, 100vw" class="aspect-[4/5] rounded-2xl lg:sticky lg:top-28" />
            </div>
        </div>
    </section>

    <!-- Facettes -->
    <section class="py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Qui je suis" titre="Ma façon d’être photographe" manuscrit="Douceur, bienveillance & patience" />
            <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                <article v-for="facette in facettes" :key="facette.titre" class="rounded-2xl border border-creme-300 bg-creme-50 p-8">
                    <span class="grid size-14 place-items-center rounded-full bg-poudre-100 text-cacao-800">
                        <Icone :nom="facette.icone" class="size-7" />
                    </span>
                    <h3 class="mt-5 text-xl">{{ facette.titre }}</h3>
                    <p class="mt-3 text-[0.97rem] leading-relaxed text-cacao-700">{{ facette.texte }}</p>
                </article>
            </div>
        </div>
    </section>

    <!-- Pourquoi me confier vos souvenirs -->
    <section class="border-y border-creme-300 bg-creme-200/60 py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Confiance" titre="Pourquoi me confier vos souvenirs ?" />
            <ul class="mt-14 grid gap-x-10 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                <li v-for="raison in raisons" :key="raison.titre" class="flex gap-5">
                    <span class="grid size-14 shrink-0 place-items-center rounded-full bg-creme-50 text-cacao-800">
                        <Icone :nom="raison.icone" class="size-7" />
                    </span>
                    <div>
                        <h3 class="text-xl">{{ raison.titre }}</h3>
                        <p class="mt-2 text-[0.97rem] leading-relaxed text-cacao-700">{{ raison.texte }}</p>
                    </div>
                </li>
            </ul>
            <!-- À compléter si besoin : expérience, formations suivies, matériel, studio. N'y mettre que du vérifiable. -->
            <div class="mt-14 flex flex-wrap justify-center gap-4">
                <Bouton :href="route('nouveau-ne') + '#securite'" variante="contour">La sécurité de bébé</Bouton>
                <Bouton :href="route('portfolio')" variante="contour">Voir le portfolio</Bouton>
            </div>
        </div>
    </section>

    <AppelReservation titre="Faisons connaissance" texte="Vous attendez un bébé, il vient d’arriver, ou vous avez simplement envie de belles photos de famille ? Écrivez-moi, j’ai hâte de vous lire." />
</template>
