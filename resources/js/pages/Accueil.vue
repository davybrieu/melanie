<script setup>
import { Link } from '@inertiajs/vue3';
import { computed, inject } from 'vue';
import AppelReservation from '../components/AppelReservation.vue';
import Bouton from '../components/Bouton.vue';
import Fleur from '../components/Fleur.vue';
import Icone from '../components/Icone.vue';
import Photo from '../components/Photo.vue';
import Pinceau from '../components/Pinceau.vue';
import Seo from '../components/Seo.vue';
import Separateur from '../components/Separateur.vue';
import TitreSection from '../components/TitreSection.vue';
import { useSite } from '../composables/useSite';
import { euros, photoSupplementaire, photosIncluses, seances } from '../data/seances';
import { entrepriseJsonLd } from '../data/seo';

const props = defineProps({
    photos: { type: Object, required: true },
});

const route = inject('route');
const site = useSite();

const univers = [
    { ...seances.grossesse, texte: 'Le ventre qui s’arrondit, les mains qui s’y posent, la douceur de l’attente.' },
    { ...seances['nouveau-ne'], texte: 'Les petits doigts, les premiers bâillements, une bulle de tendresse.' },
    { ...seances.famille, texte: 'Les rires, les câlins, les éclats de vie : votre famille telle qu’elle est.' },
];

const engagements = [
    { icone: 'coeur', texte: 'Des images naturelles & authentiques' },
    { icone: 'appareil', texte: 'Douceur, bienveillance & patience' },
    { icone: 'brin', texte: 'Une expérience simple & agréable' },
    { icone: 'coeurs', texte: 'Des souvenirs qui vous ressemblent' },
];

const offres = Object.values(seances);
const prixMini = Math.min(...offres.map((offre) => offre.prix));

const mosaique = computed(() => (props.photos.mosaique.length ? props.photos.mosaique : Array(6).fill(null)));
</script>

<template>
    <Seo
        titre="Photographe grossesse, nouveau-né & famille à Dijon"
        :description="`Mélanie Photographie : photographe grossesse, nouveau-né et famille à Dijon et Chenôve. Des images douces et naturelles pour garder vos plus beaux moments de vie. Séances dès ${euros(prixMini)}.`"
        :json-ld="entrepriseJsonLd(site)"
    />

    <!-- Hero -->
    <section class="relative isolate overflow-hidden">
        <div class="grid lg:min-h-[calc(100svh-5rem)] lg:grid-cols-[minmax(0,5fr)_minmax(0,7fr)]">
            <div class="relative order-2 flex items-center px-5 py-16 sm:px-12 lg:order-1 lg:py-20">
                <Fleur variante="gypsophile" class="absolute -top-8 -left-12 -z-10 w-44 opacity-80 sm:w-60" />
                <div class="mx-auto max-w-lg text-center lg:text-left">
                    <img src="/images/marque/monogramme-mp.webp" width="480" height="252" alt="" class="mx-auto h-14 w-auto lg:mx-0" />
                    <h1 class="mt-8">
                        <span class="block font-serif text-[2.9rem] leading-[1.05] tracking-[0.16em] text-cacao-800 uppercase sm:text-6xl">Mélanie</span>
                        <span class="manuscrit mt-1 block text-[3.4rem] text-cacao-700 sm:text-[4.4rem]">Photographie</span>
                        <span class="mt-5 block font-sans text-[0.8rem] leading-relaxed font-normal tracking-[0.3em] text-balance text-taupe-600 uppercase sm:text-sm">
                            Photographe grossesse, nouveau-né &amp; famille
                        </span>
                    </h1>
                    <p class="mt-5 inline-flex items-center gap-2 text-taupe-600">
                        <Icone nom="localisation" class="size-5 text-or-500" />
                        Déplacements sur Dijon et alentours
                    </p>
                    <p class="mt-7">
                        <span class="manuscrit relative isolate inline-block px-6 pt-3 pb-1 text-[1.95rem] sm:text-[2.5rem]">
                            <Pinceau class="text-poudre-200" />
                            Des souvenirs vrais, remplis d’émotions
                        </span>
                    </p>
                    <div class="mt-10 flex flex-wrap justify-center gap-4 lg:justify-start">
                        <Bouton :href="route('a-propos')">Découvrir mon univers</Bouton>
                        <Bouton :href="route('contact')" variante="contour">Parler de votre projet</Bouton>
                    </div>
                </div>
            </div>

            <Photo
                :photo="photos.hero"
                prioritaire
                sizes="(min-width: 1024px) 58vw, 100vw"
                libelle="Grande photo d’accueil"
                class="order-1 aspect-[4/5] sm:aspect-[3/2] lg:order-2 lg:aspect-auto lg:h-full"
            />
        </div>
    </section>

    <!-- Positionnement -->
    <section class="py-20 sm:py-28">
        <div class="conteneur max-w-3xl text-center">
            <Separateur icone="appareil" />
            <p class="mt-10 font-serif text-[1.6rem] leading-snug text-balance text-cacao-800 sm:text-[2.1rem]">
                Je photographie les histoires qui commencent, les ventres qui s’arrondissent, les petits doigts qui s’accrochent et les familles telles qu’elles sont aujourd’hui.
            </p>
            <p class="surtitre mt-10 flex flex-wrap items-center justify-center gap-x-4 gap-y-2 text-[0.8rem]">
                <Link :href="route('grossesse')" class="hover:text-cacao-800">Grossesse</Link>
                <span class="text-or-500">—</span>
                <Link :href="route('nouveau-ne')" class="hover:text-cacao-800">Nouveau-né</Link>
                <span class="text-or-500">—</span>
                <Link :href="route('famille')" class="hover:text-cacao-800">Famille</Link>
            </p>
        </div>
    </section>

    <!-- Les trois univers -->
    <section class="pb-24 sm:pb-32">
        <div class="conteneur">
            <h2 class="sr-only">Mes séances photo</h2>
            <div class="grid gap-14 md:grid-cols-3 md:gap-8 lg:gap-12">
                <article v-for="seance in univers" :key="seance.cle" class="group text-center">
                    <Link :href="route(seance.route)" class="block">
                        <Photo
                            :photo="photos.univers[seance.cle]"
                            :libelle="`Photo ${seance.nom.toLowerCase()}`"
                            sizes="(min-width: 768px) 30vw, 100vw"
                            class="mx-auto aspect-[4/5] max-w-sm rounded-t-full transition-shadow duration-500 group-hover:shadow-[0_24px_50px_-30px_rgba(75,52,40,0.6)]"
                        />
                        <h3 class="mt-7 text-2xl tracking-[0.14em] uppercase">{{ seance.nom }}</h3>
                        <p class="manuscrit mt-1 text-[2.1rem]">{{ seance.accroche }}</p>
                        <p class="mx-auto mt-3 max-w-xs text-[0.97rem] leading-relaxed text-cacao-700">{{ seance.texte }}</p>
                        <span class="mt-5 inline-flex items-center gap-2 text-[0.7rem] tracking-[0.24em] text-cacao-800 uppercase">
                            Découvrir
                            <Icone nom="fleche" class="size-4 transition-transform duration-300 group-hover:translate-x-1" />
                        </span>
                    </Link>
                </article>
            </div>
        </div>
    </section>

    <!-- Pourquoi me choisir -->
    <section class="border-y border-creme-300 bg-creme-50 py-20 sm:py-24">
        <div class="conteneur">
            <h2 class="text-center font-script text-[3.2rem] leading-none text-taupe-600 sm:text-6xl">Pourquoi me choisir ?</h2>
            <ul class="mt-12 grid grid-cols-2 gap-x-6 gap-y-10 lg:grid-cols-4">
                <li v-for="engagement in engagements" :key="engagement.texte" class="text-center">
                    <span class="mx-auto grid size-20 place-items-center rounded-full bg-poudre-100 text-cacao-800">
                        <Icone :nom="engagement.icone" class="size-9" />
                    </span>
                    <p class="mx-auto mt-4 max-w-[12rem] text-[0.97rem] leading-snug text-cacao-700">{{ engagement.texte }}</p>
                </li>
            </ul>
        </div>
    </section>

    <!-- Aperçu du portfolio -->
    <section class="py-24 sm:py-32">
        <div class="conteneur">
            <TitreSection surtitre="Portfolio" titre="Un aperçu de mon travail" manuscrit="Des moments éphémères, des souvenirs pour toujours" />
            <div class="mt-14 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3">
                <Photo
                    v-for="(photo, i) in mosaique"
                    :key="photo?.src ?? i"
                    :photo="photo"
                    sizes="(min-width: 768px) 33vw, 50vw"
                    class="aspect-[4/5]"
                    :class="i % 3 === 1 ? 'md:translate-y-8' : ''"
                />
            </div>
            <div class="mt-16 text-center md:mt-20">
                <Bouton :href="route('portfolio')" variante="contour">Voir le portfolio</Bouton>
            </div>
        </div>
    </section>

    <!-- Mon univers -->
    <section class="relative isolate overflow-hidden bg-creme-200 py-24 sm:py-32">
        <div class="conteneur grid items-center gap-14 lg:grid-cols-2 lg:gap-20">
            <div class="relative isolate mx-auto w-full max-w-md">
                <Photo :photo="photos.portrait" libelle="Votre portrait" sizes="(min-width: 1024px) 40vw, 90vw" class="aspect-[4/5] rounded-t-full" />
                <!-- Derrière la photo : son voile aquarelle ne doit pas passer sur l'image -->
                <Fleur variante="gypsophile-coeur" class="absolute -right-12 -bottom-20 -z-10 w-36 sm:w-44" />
            </div>
            <div class="text-center lg:text-left">
                <p class="surtitre">Mon univers</p>
                <h2 class="mt-3 text-[2.2rem] leading-tight sm:text-5xl">Bonjour, moi c’est Mélanie</h2>
                <p class="texte-courant mt-6">
                    J’ai {{ site.age }} ans et je vis à Chenôve, tout près de Dijon. Je photographie la grossesse, les premiers jours de bébé et les familles, avec une envie simple : créer des images douces et naturelles, dans lesquelles vous vous reconnaissez vraiment.
                </p>
                <p class="texte-courant mt-4">Vous vivez le moment, je m’occupe de le garder pour toujours.</p>
                <div class="mt-9">
                    <Bouton :href="route('a-propos')" variante="lien">Découvrir mon univers</Bouton>
                </div>
            </div>
        </div>
    </section>

    <!-- Tarifs -->
    <section class="py-24 sm:py-32">
        <div class="conteneur">
            <TitreSection surtitre="Mes offres" coeurs titre="Tarifs" majuscules />
            <ul class="mt-12 grid gap-5 md:grid-cols-3">
                <li v-for="offre in offres" :key="offre.cle" class="flex flex-col rounded-2xl border border-creme-300 bg-creme-50 px-6 py-8 text-center">
                    <span class="mx-auto grid size-16 place-items-center rounded-full bg-poudre-100 text-cacao-800">
                        <Icone :nom="offre.icone" class="size-8" />
                    </span>
                    <h3 class="mt-5 text-lg tracking-[0.1em] uppercase">{{ offre.nom }}</h3>
                    <p class="manuscrit mt-1 text-[1.75rem]">{{ offre.accroche }}</p>
                    <p class="mt-auto pt-4 font-serif text-4xl text-brique">{{ euros(offre.prix) }}</p>
                </li>
            </ul>
            <p class="mt-8 text-center text-[0.95rem] text-taupe-600">
                Prêt de tenues et {{ photosIncluses }} photos retouchées inclus · Photo supplémentaire : {{ euros(photoSupplementaire) }}
            </p>
            <div class="mt-10 text-center">
                <Bouton :href="route('tarifs')" variante="contour">Voir les tarifs en détail</Bouton>
            </div>

            <div class="mt-20 flex flex-col items-center gap-6 rounded-2xl bg-poudre-100 px-8 py-10 text-center sm:flex-row sm:text-left">
                <span class="grid size-16 shrink-0 place-items-center rounded-full bg-creme-50 text-cacao-800">
                    <Icone nom="cadeau" class="size-8" />
                </span>
                <div class="flex-1">
                    <h2 class="text-2xl">Offrir une séance photo</h2>
                    <p class="mt-1 text-cacao-700">Pour une future maman, une naissance ou une famille : un souvenir qui dure toute la vie.</p>
                </div>
                <Bouton :href="route('bon-cadeau')">Le bon cadeau</Bouton>
            </div>
        </div>
    </section>

    <AppelReservation />
</template>
