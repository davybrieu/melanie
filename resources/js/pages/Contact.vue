<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, inject } from 'vue';
import Bouton from '../components/Bouton.vue';
import FilAriane from '../components/FilAriane.vue';
import Fleur from '../components/Fleur.vue';
import Icone from '../components/Icone.vue';
import Seo from '../components/Seo.vue';
import TitreSection from '../components/TitreSection.vue';
import { useSite } from '../composables/useSite';
import { entrepriseJsonLd } from '../data/seo';

const props = defineProps({
    seance: { type: String, default: null },
    objet: { type: String, default: null },
});

const route = inject('route');
const page = usePage();
const site = useSite();

const types = [
    { cle: 'grossesse', nom: 'Grossesse', icone: 'grossesse' },
    { cle: 'nouveau-ne', nom: 'Nouveau-né', icone: 'bebe' },
    { cle: 'famille', nom: 'Famille', icone: 'famille' },
];

const form = useForm({
    prenom: '',
    nom: '',
    email: '',
    telephone: '',
    seances: props.seance ? [props.seance] : [],
    date_accouchement: '',
    nombre_personnes: '',
    periode: '',
    message: props.objet === 'bon-cadeau' ? 'Bonjour, je souhaiterais offrir un bon cadeau pour une séance ' : '',
    site_web: '',
});

// La date d'accouchement ne concerne que les séances grossesse et nouveau-né.
const avecTerme = computed(() => form.seances.some((seance) => seance !== 'famille'));

const envoyee = computed(() => page.flash?.demandeEnvoyee);

function envoyer() {
    form.transform((donnees) => ({ ...donnees, date_accouchement: avecTerme.value ? donnees.date_accouchement : '' })).post(route('contact.envoyer'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

const champ =
    'mt-2 block w-full rounded-xl border border-creme-300 bg-creme-100/70 px-4 py-3 text-[1rem] font-normal text-cacao-800 transition-colors placeholder:font-light placeholder:text-taupe-400 focus:border-or-500 focus:bg-creme-50 focus:ring-2 focus:ring-or-300/40 focus:outline-none';
const etiquette = 'text-[0.72rem] font-normal tracking-[0.2em] text-cacao-700 uppercase';
</script>

<template>
    <Seo
        titre="Contact – réserver une séance photo à Dijon"
        description="Parlons de votre projet de séance photo grossesse, nouveau-né ou famille à Dijon et alentours. Écrivez à Mélanie Brieu via le formulaire ou sur Instagram."
        :json-ld="entrepriseJsonLd(site)"
    />

    <section class="relative isolate overflow-hidden pt-8 pb-24 sm:pb-32">
        <Fleur variante="pampa" class="absolute top-16 -right-12 -z-10 hidden w-56 opacity-70 md:block" />
        <Fleur variante="gypsophile" class="absolute bottom-10 -left-14 -z-10 hidden w-48 opacity-70 md:block" />

        <div class="conteneur max-w-3xl">
            <FilAriane :liens="[{ libelle: 'Contact' }]" />

            <TitreSection class="mt-12" balise="h1" surtitre="Contact" titre="Parlons de votre projet" manuscrit="J’ai hâte de vous lire" />

            <div v-if="page.flash?.sessionExpiree" class="mx-auto mt-10 max-w-2xl rounded-xl bg-poudre-100 px-5 py-4 text-center text-cacao-800" role="status">
                La page était ouverte depuis longtemps : pouvez-vous renvoyer votre message ?
            </div>

            <!-- Confirmation -->
            <div v-if="envoyee" class="mx-auto mt-12 max-w-2xl rounded-2xl border border-creme-300 bg-creme-50 px-8 py-14 text-center" role="status">
                <span class="mx-auto grid size-16 place-items-center rounded-full bg-poudre-100 text-brique">
                    <Icone nom="coeur" class="size-8" />
                </span>
                <h2 class="mt-6 text-3xl">Merci, votre demande est bien envoyée !</h2>
                <p class="texte-courant mt-4">Je vous réponds généralement sous {{ site.delai_reponse }} heures. À très vite !</p>
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <Bouton :href="route('portfolio')" variante="contour">Voir le portfolio</Bouton>
                    <Bouton :href="site.instagramUrl" externe variante="contour" icone="instagram">Instagram</Bouton>
                </div>
            </div>

            <!-- Formulaire -->
            <form
                v-else
                class="mt-12 rounded-2xl border border-creme-300 bg-creme-50 p-6 shadow-[0_18px_40px_-30px_rgba(75,52,40,0.35)] sm:p-10"
                novalidate
                @submit.prevent="envoyer"
            >
                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="prenom" :class="etiquette">Prénom <span class="text-rouge" aria-hidden="true">*</span></label>
                        <input id="prenom" v-model="form.prenom" type="text" autocomplete="given-name" placeholder="Ex. : Léa" required :class="champ" :aria-invalid="!!form.errors.prenom" aria-describedby="erreur-prenom" />
                        <p v-if="form.errors.prenom" id="erreur-prenom" class="mt-2 text-sm text-brique">{{ form.errors.prenom }}</p>
                    </div>
                    <div>
                        <label for="nom" :class="etiquette">Nom <span class="text-rouge" aria-hidden="true">*</span></label>
                        <input id="nom" v-model="form.nom" type="text" autocomplete="family-name" placeholder="Ex. : Martin" required :class="champ" :aria-invalid="!!form.errors.nom" aria-describedby="erreur-nom" />
                        <p v-if="form.errors.nom" id="erreur-nom" class="mt-2 text-sm text-brique">{{ form.errors.nom }}</p>
                    </div>
                    <div>
                        <label for="email" :class="etiquette">E-mail <span class="text-rouge" aria-hidden="true">*</span></label>
                        <input id="email" v-model="form.email" type="email" autocomplete="email" placeholder="Ex. : lea.martin@exemple.fr" required :class="champ" :aria-invalid="!!form.errors.email" aria-describedby="erreur-email" />
                        <p v-if="form.errors.email" id="erreur-email" class="mt-2 text-sm text-brique">{{ form.errors.email }}</p>
                    </div>
                    <div>
                        <label for="telephone" :class="etiquette">Téléphone</label>
                        <input id="telephone" v-model="form.telephone" type="tel" autocomplete="tel" placeholder="Ex. : 06 12 34 56 78" :class="champ" :aria-invalid="!!form.errors.telephone" aria-describedby="erreur-telephone" />
                        <p v-if="form.errors.telephone" id="erreur-telephone" class="mt-2 text-sm text-brique">{{ form.errors.telephone }}</p>
                    </div>
                </div>

                <fieldset class="mt-8">
                    <legend :class="etiquette">Quel type de séance ? <span class="text-rouge" aria-hidden="true">*</span><span class="sr-only">(obligatoire)</span></legend>
                    <div class="mt-3 grid grid-cols-3 gap-3">
                        <label v-for="type in types" :key="type.cle" class="relative cursor-pointer">
                            <input v-model="form.seances" type="checkbox" :value="type.cle" class="peer sr-only" />
                            <span
                                class="flex flex-col items-center gap-2 rounded-xl border border-creme-300 bg-creme-100/70 px-2 py-4 text-center text-[0.95rem] text-cacao-700 transition-colors peer-checked:border-cacao-700 peer-checked:bg-poudre-100 peer-checked:text-cacao-900 peer-focus-visible:ring-2 peer-focus-visible:ring-or-400 hover:border-or-400"
                            >
                                <Icone :nom="type.icone" class="size-8" />
                                {{ type.nom }}
                            </span>
                            <Icone nom="check" class="absolute top-2 right-2 size-4 text-brique opacity-0 transition-opacity peer-checked:opacity-100" />
                        </label>
                    </div>
                    <p v-if="form.errors.seances" class="mt-2 text-sm text-brique">{{ form.errors.seances }}</p>
                </fieldset>

                <div v-if="avecTerme" class="mt-8">
                    <label for="date_accouchement" :class="etiquette">Date prévue d’accouchement</label>
                    <input id="date_accouchement" v-model="form.date_accouchement" type="date" :class="champ" aria-describedby="aide-date erreur-date" />
                    <p id="aide-date" class="mt-2 text-sm text-taupe-500">Cette information sert uniquement à planifier votre séance.</p>
                    <p v-if="form.errors.date_accouchement" id="erreur-date" class="mt-1 text-sm text-brique">{{ form.errors.date_accouchement }}</p>
                </div>

                <div class="mt-8 grid gap-6 sm:grid-cols-[minmax(0,0.6fr)_minmax(0,1fr)]">
                    <div>
                        <label for="nombre_personnes" :class="etiquette">Nombre de personnes</label>
                        <input id="nombre_personnes" v-model="form.nombre_personnes" type="number" min="1" max="30" inputmode="numeric" placeholder="Ex. : 4" :class="champ" />
                        <p v-if="form.errors.nombre_personnes" class="mt-2 text-sm text-brique">{{ form.errors.nombre_personnes }}</p>
                    </div>
                    <div>
                        <label for="periode" :class="etiquette">Période souhaitée</label>
                        <input id="periode" v-model="form.periode" type="text" placeholder="Ex. : fin mai, un samedi matin…" :class="champ" />
                        <p v-if="form.errors.periode" class="mt-2 text-sm text-brique">{{ form.errors.periode }}</p>
                    </div>
                </div>

                <div class="mt-8">
                    <label for="message" :class="etiquette">Votre message</label>
                    <textarea id="message" v-model="form.message" rows="6" placeholder="Parlez-moi de vous, de vos envies, de vos questions…" :class="champ"></textarea>
                    <p v-if="form.errors.message" class="mt-2 text-sm text-brique">{{ form.errors.message }}</p>
                </div>

                <!-- Piège à robots : invisible pour les humains -->
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label for="site_web">Ne pas remplir</label>
                    <input id="site_web" v-model="form.site_web" type="text" tabindex="-1" autocomplete="off" />
                </div>

                <div class="mt-10 text-center">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2.5 rounded-full bg-cacao-800 px-9 py-4 text-[0.75rem] font-normal tracking-[0.24em] text-creme-50 uppercase transition-colors duration-300 hover:bg-brique disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        <Icone nom="coeur" class="size-4" />
                        {{ form.processing ? 'Envoi en cours…' : 'Envoyer ma demande' }}
                    </button>
                    <p class="mt-6 text-[0.95rem] text-cacao-700">Je vous réponds généralement sous {{ site.delai_reponse }} heures.</p>
                </div>
            </form>
        </div>
    </section>
</template>
