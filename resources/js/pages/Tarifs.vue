<script setup>
import { computed, inject } from 'vue';
import AppelReservation from '../components/AppelReservation.vue';
import Bouton from '../components/Bouton.vue';
import CarteTarif from '../components/CarteTarif.vue';
import Faq from '../components/Faq.vue';
import FilAriane from '../components/FilAriane.vue';
import Fleur from '../components/Fleur.vue';
import Icone from '../components/Icone.vue';
import Photo from '../components/Photo.vue';
import Seo from '../components/Seo.vue';
import TitreSection from '../components/TitreSection.vue';
import { useSite } from '../composables/useSite';
import { faqTarifs } from '../data/faq';
import { euros, pack, photoSupplementaire, seances } from '../data/seances';
import { faqJsonLd, serviceJsonLd } from '../data/seo';

defineProps({
    photos: { type: Object, required: true },
});

const route = inject('route');
const site = useSite();

const offres = [seances.grossesse, seances['nouveau-ne'], seances.famille, pack];

// Questions tarifs les plus utiles ici ; la liste complète est sur la page FAQ.
const questions = faqTarifs.filter((_, i) => [1, 2, 3, 4, 7, 8, 10, 14].includes(i));

const jsonLd = computed(() => [
    ...offres.map((offre) =>
        serviceJsonLd(site.value, {
            nom: offre.titre,
            description: `${offre.titre} à Dijon : ${offre.inclus.join(', ').toLowerCase()}.`,
            prix: offre.prix,
            url: route('tarifs'),
        }),
    ),
    faqJsonLd(questions),
]);

const engagements = [
    { icone: 'coeur', texte: 'Des images naturelles & authentiques' },
    { icone: 'appareil', texte: 'Douceur, bienveillance & patience' },
    { icone: 'brin', texte: 'Une expérience simple & agréable' },
    { icone: 'coeurs', texte: 'Des souvenirs qui vous ressemblent' },
];
</script>

<template>
    <Seo
        titre="Tarifs des séances photo grossesse, nouveau-né & famille"
        :description="`Tarifs de lancement : séance grossesse ${euros(seances.grossesse.prix)}, nouveau-né ${euros(seances['nouveau-ne'].prix)}, famille ${euros(seances.famille.prix)}, pack grossesse + nouveau-né ${euros(pack.prix)}. Prêt de tenues et 10 photos retouchées inclus.`"
        :json-ld="jsonLd"
    />

    <section class="relative isolate overflow-hidden pt-8 pb-20 sm:pb-28">
        <Fleur variante="pampa" class="absolute top-6 -right-10 -z-10 w-40 opacity-80 sm:w-60" />
        <Fleur variante="gypsophile" class="absolute top-28 -left-14 -z-10 w-36 opacity-70 sm:w-48" />

        <div class="conteneur">
            <FilAriane :liens="[{ libelle: 'Tarifs' }]" />

            <div class="mt-10 text-center">
                <img src="/images/marque/monogramme-mb.webp" width="480" height="253" alt="" class="mx-auto h-12 w-auto" />
                <TitreSection class="mt-4" balise="h1" surtitre="Offre de lancement" coeurs titre="Tarifs" majuscules manuscrit="Des souvenirs vrais, remplis d’émotions" />
                <p class="surtitre mt-8 flex flex-wrap items-center justify-center gap-x-4 gap-y-2">
                    <span>Grossesse</span><span class="text-or-500">·</span><span>Nouveau-né</span><span class="text-or-500">·</span><span>Famille</span>
                </p>
                <p class="mt-4 inline-flex items-center gap-2 text-sm tracking-[0.2em] text-cacao-700 uppercase">
                    <Icone nom="localisation" class="size-5 text-or-500" /> {{ site.commune }}
                </p>
            </div>

            <div class="mt-14 grid gap-6 lg:grid-cols-[minmax(0,0.42fr)_minmax(0,1fr)] lg:gap-10">
                <div class="hidden flex-col gap-4 lg:flex">
                    <Photo :photo="photos.grossesse" libelle="Photo grossesse" sizes="30vw" class="aspect-[5/4] flex-1 rounded-sm" />
                    <Photo :photo="photos['nouveau-ne']" libelle="Photo nouveau-né" sizes="30vw" class="aspect-[5/4] flex-1 rounded-sm" />
                    <Photo :photo="photos.famille" libelle="Photo famille" sizes="30vw" class="aspect-[5/4] flex-1 rounded-sm" />
                </div>
                <div class="space-y-5">
                    <CarteTarif v-for="offre in offres" :key="offre.cle" :offre="offre" balise="h2" />
                </div>
            </div>

            <!-- Photo supplémentaire · Pourquoi me choisir · Infos -->
            <div class="mt-10 grid gap-5 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.6fr)_minmax(0,0.9fr)]">
                <div class="rounded-2xl border border-creme-300 bg-creme-50 p-7 text-center lg:text-left">
                    <p class="inline-flex items-center gap-3 text-[0.8rem] tracking-[0.18em] text-cacao-800 uppercase">
                        <Icone nom="image" class="size-6 text-cacao-700" /> Photo supplémentaire
                    </p>
                    <p class="mt-4 font-serif text-3xl text-brique">{{ euros(photoSupplementaire) }} <span class="font-sans text-base text-taupe-600">/ photo</span></p>
                    <p class="mt-2 text-sm text-taupe-600">À choisir dans votre galerie, en plus des photos incluses.</p>
                </div>

                <div class="rounded-2xl border border-creme-300 bg-creme-50 p-7">
                    <h2 class="text-center font-script text-[2.6rem] leading-none text-taupe-600">Pourquoi me choisir ?</h2>
                    <ul class="mt-6 grid grid-cols-2 gap-6 sm:grid-cols-4">
                        <li v-for="engagement in engagements" :key="engagement.texte" class="text-center">
                            <span class="mx-auto grid size-14 place-items-center rounded-full bg-poudre-100 text-cacao-800">
                                <Icone :nom="engagement.icone" class="size-7" />
                            </span>
                            <p class="mt-3 text-[0.85rem] leading-snug text-cacao-700">{{ engagement.texte }}</p>
                        </li>
                    </ul>
                </div>

                <div class="rounded-2xl border border-creme-300 bg-creme-50 p-7">
                    <p class="surtitre text-center">Infos</p>
                    <ul class="mt-5 space-y-4 text-[0.97rem]">
                        <li class="flex items-center gap-3"><Icone nom="localisation" class="size-6 shrink-0 text-cacao-700" /> {{ site.commune }}</li>
                        <li class="flex items-center gap-3 border-t border-dashed border-creme-300 pt-4">
                            <Icone nom="voiture" class="size-6 shrink-0 text-cacao-700" /> Je me déplace sur Dijon et alentours
                        </li>
                        <li class="flex items-center gap-3 border-t border-dashed border-creme-300 pt-4">
                            <Icone nom="cadeau" class="size-6 shrink-0 text-cacao-700" />
                            <span>Séances à offrir : <Bouton :href="route('bon-cadeau')" variante="lien" class="mt-1">Bon cadeau</Bouton></span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-creme-50 py-20 sm:py-28">
        <div class="conteneur max-w-3xl">
            <TitreSection surtitre="Questions fréquentes" titre="Réservation, paiement, photos" />
            <div class="mt-12">
                <Faq :questions="questions" />
            </div>
            <p class="mt-10 text-center">
                <Bouton :href="route('faq') + '#reservations-tarifs'" variante="lien">Toutes les questions sur les tarifs</Bouton>
            </p>
        </div>
    </section>

    <AppelReservation titre="Réservez votre séance" />
</template>
