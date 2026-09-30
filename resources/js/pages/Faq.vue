<script setup>
import { inject } from 'vue';
import AppelReservation from '../components/AppelReservation.vue';
import Faq from '../components/Faq.vue';
import FilAriane from '../components/FilAriane.vue';
import Seo from '../components/Seo.vue';
import TitreSection from '../components/TitreSection.vue';
import { categoriesFaq } from '../data/faq';
import { faqJsonLd } from '../data/seo';

const route = inject('route');

const toutes = categoriesFaq.flatMap((categorie) => categorie.questions);
</script>

<template>
    <Seo
        titre="Questions fréquentes – séances photo grossesse, nouveau-né & famille"
        description="Déroulement des séances, tenues, préparation de bébé et des enfants, tarifs, réservation, livraison des photos : toutes les réponses de Mélanie Photographie, à Dijon."
        :json-ld="faqJsonLd(toutes)"
    />

    <section class="pt-8 pb-20 sm:pb-28">
        <div class="conteneur">
            <FilAriane :liens="[{ libelle: 'Questions fréquentes' }]" />
            <TitreSection class="mt-12" balise="h1" surtitre="FAQ" titre="Questions fréquentes" manuscrit="Tout ce que vous voulez savoir">
                <p class="texte-courant mx-auto mt-6 max-w-2xl">
                    Vous vous posez une question avant de réserver ? Vous trouverez sûrement la réponse ici. Sinon, écrivez-moi : je vous réponds avec plaisir.
                </p>
            </TitreSection>

            <div class="mt-16 grid gap-12 lg:grid-cols-[14rem_minmax(0,1fr)] lg:gap-16">
                <nav aria-label="Catégories de questions" class="lg:sticky lg:top-28 lg:self-start">
                    <ul class="flex flex-wrap justify-center gap-2 lg:flex-col lg:gap-1">
                        <li v-for="categorie in categoriesFaq" :key="categorie.id">
                            <a
                                :href="`#${categorie.id}`"
                                class="block rounded-full border border-creme-300 bg-creme-50 px-4 py-2 text-[0.72rem] tracking-[0.18em] text-cacao-700 uppercase transition-colors hover:border-or-400 hover:text-cacao-900 lg:rounded-none lg:border-0 lg:border-l lg:border-creme-300 lg:bg-transparent lg:px-4 lg:py-2.5 lg:hover:border-or-500"
                            >
                                {{ categorie.titre }}
                                <span class="text-taupe-400">({{ categorie.questions.length }})</span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <div class="space-y-20">
                    <section v-for="categorie in categoriesFaq" :id="categorie.id" :key="categorie.id" :aria-labelledby="`titre-${categorie.id}`">
                        <h2 :id="`titre-${categorie.id}`" class="text-[1.9rem] sm:text-4xl">{{ categorie.titre }}</h2>
                        <div class="mt-6">
                            <Faq :questions="categorie.questions" />
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </section>

    <AppelReservation titre="Vous n’avez pas trouvé votre réponse ?" texte="Écrivez-moi : je vous réponds avec plaisir, et nous pourrons parler de votre projet de séance." :href="route('contact')" bouton="Poser ma question" />
</template>
