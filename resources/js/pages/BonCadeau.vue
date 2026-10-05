<script setup>
import { inject } from 'vue';
import AppelReservation from '../components/AppelReservation.vue';
import Bouton from '../components/Bouton.vue';
import EnTetePage from '../components/EnTetePage.vue';
import Etapes from '../components/Etapes.vue';
import Icone from '../components/Icone.vue';
import Seo from '../components/Seo.vue';
import TitreSection from '../components/TitreSection.vue';
import { useSite } from '../composables/useSite';
import { conditions } from '../data/conditions';
import { euros, seances } from '../data/seances';
import { bonCadeauJsonLd } from '../data/seo';

defineProps({
    photo: { type: Object, default: null },
});

const route = inject('route');
const site = useSite();

const description = `Offrez une séance photo grossesse, nouveau-né ou famille à Dijon avec un bon cadeau valable ${conditions.validiteBonCadeau}. Un souvenir pour la vie, dès ${euros(seances.famille.prix)}.`;

const offres = [seances.grossesse, seances['nouveau-ne'], seances.famille];

const etapes = [
    { titre: 'Choisissez la séance', texte: 'Grossesse, nouveau-né ou famille : dites-moi ce qui ferait plaisir.' },
    { titre: 'Écrivez-moi', texte: 'Via le formulaire ou sur Instagram, en précisant le prénom de la personne gâtée.' },
    { titre: 'Recevez le bon', texte: 'Je vous prépare un joli bon cadeau personnalisé, prêt à être offert.' },
    { titre: 'Place à la séance', texte: `L’heureux bénéficiaire me contacte pour réserver sa date. Le bon est valable ${conditions.validiteBonCadeau}.` },
];

const occasions = ['Une future maman', 'Une naissance', 'La fête des mères', 'Un anniversaire', 'Noël', 'Des grands-parents gâteux'];
</script>

<template>
    <Seo
        titre="Bon cadeau séance photo à Dijon"
        :description="description"
        :json-ld="bonCadeauJsonLd(site, route, description)"
    />

    <EnTetePage
        :fil="[{ libelle: 'Bon cadeau' }]"
        surtitre="Bon cadeau"
        titre="Offrir une séance photo"
        manuscrit="Un souvenir pour la vie"
        :photo="photo"
        libelle-photo="Photo bon cadeau"
    >
        <p class="texte-courant mx-auto mt-7 max-w-xl lg:mx-0">
            Plutôt qu’un objet de plus, offrez un moment et des images qui se regarderont pendant des années. Toutes mes séances peuvent être offertes : pour une future maman, l’arrivée d’un bébé ou une famille que vous aimez.
        </p>
        <div class="mt-9 flex flex-wrap justify-center gap-4 lg:justify-start">
            <Bouton :href="route('contact', { objet: 'bon-cadeau' })" icone="cadeau">Commander un bon cadeau</Bouton>
        </div>
    </EnTetePage>

    <section class="bg-creme-50 py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Comment ça marche ?" titre="Un cadeau simple à offrir" />
            <div class="mt-12">
                <Etapes :etapes="etapes" />
            </div>
        </div>
    </section>

    <section class="py-20 sm:py-28">
        <div class="conteneur">
            <TitreSection surtitre="Mes offres" coeurs titre="Les séances à offrir" />
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

            <div class="mt-16 rounded-2xl bg-poudre-100 px-8 py-10 text-center">
                <h2 class="text-2xl sm:text-3xl">Une jolie idée pour…</h2>
                <ul class="mt-6 flex flex-wrap justify-center gap-3">
                    <li v-for="occasion in occasions" :key="occasion" class="rounded-full bg-creme-50 px-5 py-2 text-[0.95rem] text-cacao-700">{{ occasion }}</li>
                </ul>
            </div>
        </div>
    </section>

    <AppelReservation
        :href="route('contact', { objet: 'bon-cadeau' })"
        bouton="Commander un bon cadeau"
        titre="Commander un bon cadeau"
        texte="Dites-moi quelle séance vous souhaitez offrir et pour qui : je vous prépare un bon cadeau personnalisé."
    />
</template>
