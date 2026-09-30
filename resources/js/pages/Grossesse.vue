<script setup>
import { computed, inject } from 'vue';
import AppelReservation from '../components/AppelReservation.vue';
import Bouton from '../components/Bouton.vue';
import CarteTarif from '../components/CarteTarif.vue';
import EnTetePage from '../components/EnTetePage.vue';
import Etapes from '../components/Etapes.vue';
import Galerie from '../components/Galerie.vue';
import Icone from '../components/Icone.vue';
import Photo from '../components/Photo.vue';
import SectionFaq from '../components/SectionFaq.vue';
import Seo from '../components/Seo.vue';
import TitreSection from '../components/TitreSection.vue';
import { useSite } from '../composables/useSite';
import { faqGrossesse } from '../data/faq';
import { euros, photosIncluses, seances } from '../data/seances';
import { faqJsonLd, serviceJsonLd } from '../data/seo';

const props = defineProps({
    photos: { type: Object, required: true },
});

const route = inject('route');
const site = useSite();
const seance = seances.grossesse;

const description = `Séance photo grossesse à Dijon et alentours avec Mélanie Brieu : une séance douce et naturelle, en extérieur ou à domicile, prêt de tenues inclus. ${euros(seance.prix)} (offre de lancement).`;

const jsonLd = computed(() => [
    serviceJsonLd(site.value, { nom: 'Séance photo grossesse', description, prix: seance.prix, url: route('grossesse') }),
    faqJsonLd(faqGrossesse),
]);

const grandes = computed(() => [props.photos.grandes[0] ?? null, props.photos.grandes[1] ?? null]);

const etapes = [
    { titre: 'On fait connaissance', texte: 'Par message ou au téléphone, nous parlons de vos envies, du lieu, des tenues et de la date idéale.' },
    { titre: 'Le jour de la séance', texte: 'Environ une heure, à votre rythme. Je vous guide pas à pas : inutile de savoir poser.' },
    { titre: 'Votre sélection', texte: `Vous découvrez vos photos dans une galerie et choisissez vos ${photosIncluses} préférées.` },
    { titre: 'Vos souvenirs', texte: 'Je retouche chaque image avec soin et vous la livre en haute définition.' },
];

const infos = [
    { icone: 'calendrier', titre: 'Quand ?', texte: 'Entre le 7e et le 8e mois, quand le ventre est bien rond et que vous êtes encore à l’aise. Idéalement, réservez dès le 5e mois.' },
    { icone: 'famille', titre: 'Avec qui ?', texte: 'Seule, avec votre partenaire, avec vos aînés… Chacun a sa place dans cette histoire.' },
    { icone: 'soleil', titre: 'Où ?', texte: 'En extérieur, dans la lumière dorée de fin de journée, ou chez vous pour une ambiance cocooning. À Dijon et alentours.' },
    { icone: 'cintre', titre: 'Que porter ?', texte: 'Des matières fluides et des tons doux. Le prêt de tenues est inclus : robes et tissus pour sublimer votre silhouette.' },
    { icone: 'horloge', titre: 'Combien de temps ?', texte: `${seance.duree} de séance, sans course contre la montre, avec des pauses dès que vous en avez besoin.` },
];
</script>

<template>
    <Seo titre="Photographe grossesse à Dijon" :description="description" :json-ld="jsonLd" />

    <EnTetePage
        :fil="[{ libelle: 'Grossesse' }]"
        surtitre="Photographe grossesse à Dijon"
        titre="Séance photo grossesse à Dijon"
        :manuscrit="seance.accroche"
        :photo="photos.hero"
        libelle-photo="Photo grossesse principale"
    >
        <p class="texte-courant mx-auto mt-7 max-w-xl lg:mx-0">
            Votre ventre s’arrondit, bébé bouge, vous vous préparez à rencontrer quelqu’un que vous aimez déjà. La grossesse est une parenthèse unique, et si courte. Je vous propose une séance douce et naturelle pour garder la trace de ces mois si particuliers : seule, en couple ou avec vos aînés.
        </p>
        <div class="mt-9 flex flex-wrap justify-center gap-4 lg:justify-start">
            <Bouton :href="route('contact', { seance: 'grossesse' })">Réserver ma séance</Bouton>
        </div>
    </EnTetePage>

    <!-- Approche + grandes photos -->
    <section class="bg-creme-50 py-20 sm:py-28">
        <div class="conteneur">
            <div class="mx-auto max-w-3xl text-center">
                <TitreSection surtitre="Mon approche" titre="Un moment pour vous, rien que pour vous" />
                <p class="texte-courant mt-8">
                    Une séance photo grossesse, ce n’est pas une série de poses figées. C’est un moment pour vous : prendre le temps, vous sentir belle, célébrer ce corps qui porte la vie. Je vous guide avec douceur et je laisse la place aux gestes tendres, aux regards, aux rires, dans une lumière qui sublime votre ventre rond.
                </p>
                <p class="texte-courant mt-4">Mon souhait : que vous repartiez avec des images qui vous ressemblent, et le souvenir d’un moment agréable.</p>
            </div>
            <div class="mt-14 grid gap-4 sm:grid-cols-2 sm:gap-6">
                <Photo :photo="grandes[0]" libelle="Grande photo grossesse" sizes="(min-width: 640px) 50vw, 100vw" class="aspect-[4/5] rounded-2xl" />
                <Photo :photo="grandes[1]" libelle="Grande photo grossesse" sizes="(min-width: 640px) 50vw, 100vw" class="aspect-[4/5] rounded-2xl sm:mt-16" />
            </div>
        </div>
    </section>

    <!-- Déroulement -->
    <section class="py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Déroulement" titre="Comment se passe la séance ?" />
            <div class="mt-12">
                <Etapes :etapes="etapes" />
            </div>
        </div>
    </section>

    <!-- Infos pratiques -->
    <section class="border-y border-creme-300 bg-creme-200/60 py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Infos pratiques" titre="Quand, où, avec qui ?" />
            <ul class="mt-12 grid gap-x-10 gap-y-12 sm:grid-cols-2 lg:grid-cols-3">
                <li v-for="info in infos" :key="info.titre" class="flex gap-5">
                    <span class="grid size-14 shrink-0 place-items-center rounded-full bg-poudre-100 text-cacao-800">
                        <Icone :nom="info.icone" class="size-7" />
                    </span>
                    <div>
                        <h3 class="text-xl">{{ info.titre }}</h3>
                        <p class="mt-2 text-[0.97rem] leading-relaxed text-cacao-700">{{ info.texte }}</p>
                    </div>
                </li>
            </ul>
        </div>
    </section>

    <!-- Tarif -->
    <section id="tarif" class="py-20 sm:py-28">
        <div class="conteneur max-w-4xl">
            <TitreSection surtitre="Offre de lancement" coeurs titre="Tarif de la séance grossesse" />
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
            <TitreSection surtitre="Galerie" titre="Quelques séances grossesse" manuscrit="Des souvenirs vrais, remplis d’émotions" />
            <div class="mt-12">
                <Galerie :photos="photos.galerie" libelle="Photo grossesse" />
            </div>
            <p class="mt-10 text-center">
                <Bouton :href="route('portfolio.categorie', { categorie: 'grossesse' })" variante="contour">Voir le portfolio grossesse</Bouton>
            </p>
        </div>
    </section>

    <SectionFaq titre="Vos questions sur la séance grossesse" :questions="faqGrossesse" ancre="grossesse" />

    <AppelReservation seance="grossesse" titre="Et si on immortalisait votre ventre rond ?" />
</template>
