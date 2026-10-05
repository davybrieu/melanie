<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, inject, nextTick, ref, watch } from 'vue';
import Bouton from '../components/Bouton.vue';
import Captcha from '../components/Captcha.vue';
import Champ from '../components/Champ.vue';
import FilAriane from '../components/FilAriane.vue';
import Fleur from '../components/Fleur.vue';
import Icone from '../components/Icone.vue';
import Seo from '../components/Seo.vue';
import TitreSection from '../components/TitreSection.vue';
import { useSite } from '../composables/useSite';
import { adresse, entrepriseJsonLd, idEntreprise, pageJsonLd } from '../data/seo';

const props = defineProps({
    seance: { type: String, default: null },
    objet: { type: String, default: null },
    // Clé publique hCaptcha ; null quand la vérification est désactivée (local sans clés).
    cleCaptcha: { type: String, default: null },
});

const route = inject('route');
const page = usePage();
const site = useSite();

const jsonLd = computed(() => [
    entrepriseJsonLd(site.value),
    pageJsonLd(site.value, 'ContactPage', { nom: 'Contact', url: adresse(site.value, route, 'contact'), about: { '@id': idEntreprise(site.value) } }),
]);

const types = [
    { cle: 'grossesse', nom: 'Grossesse', icone: 'grossesse' },
    { cle: 'nouveau-ne', nom: 'Nouveau-né', icone: 'bebe' },
    { cle: 'famille', nom: 'Famille', icone: 'famille' },
    { cle: 'autres', nom: 'Autres', icone: 'appareil' },
];

const form = useForm({
    prenom: '',
    nom: '',
    telephone: '',
    seances: props.seance ? [props.seance] : [],
    nombre_personnes: '',
    periode: '',
    message: props.objet === 'bon-cadeau' ? 'Bonjour, je souhaiterais offrir un bon cadeau pour une séance ' : '',
    site_web: '',
    captcha: '',
});

const captcha = ref(null);
const envoyee = computed(() => page.flash?.demandeEnvoyee);

// Une erreur disparaît dès que la cliente modifie le champ concerné. Pour hCaptcha, seulement
// quand un nouveau jeton arrive : la remise à zéro de la case ne doit pas effacer son erreur.
watch(
    () => ({ ...form.data() }),
    (actuel, precedent) =>
        Object.keys(actuel)
            .filter((cle) => actuel[cle] !== precedent[cle] && (cle !== 'captcha' || actuel[cle]))
            .forEach((cle) => form.clearErrors(cle)),
);

// Ordre des champs à l'écran : après un envoi refusé, le premier en erreur est montré à la cliente.
const ordreChamps = ['prenom', 'nom', 'telephone', 'seances', 'nombre_personnes', 'periode', 'message', 'captcha'];

function montrerPremiereErreur(erreurs) {
    const champ = ordreChamps.find((cle) => erreurs[cle]);

    if (champ === 'captcha') {
        document.getElementById('erreur-captcha')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else if (champ) {
        document.getElementById(champ === 'seances' ? `seance-${types[0].cle}` : champ)?.focus();
    }
}

function envoyer() {
    form.post(route('contact.envoyer'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (erreurs) => {
            // Jeton refusé par hCaptcha : il ne resservira pas, la case est à cocher de nouveau.
            if (erreurs.captcha) captcha.value?.reinitialiser();
            nextTick(() => montrerPremiereErreur(erreurs));
        },
    });
}

const etiquette = 'text-[0.72rem] font-normal tracking-[0.2em] text-cacao-700 uppercase';
</script>

<template>
    <Seo
        titre="Contact – réserver une séance photo à Dijon"
        description="Parlons de votre projet de séance photo grossesse, nouveau-né ou famille à Dijon. Laissez-moi votre numéro : je vous rappelle généralement dans l’heure."
        :json-ld="jsonLd"
    />

    <section class="relative isolate overflow-hidden pt-8 pb-24 sm:pb-32">
        <Fleur variante="pampa" visible-des="md" class="absolute top-16 -right-12 -z-10 hidden w-56 opacity-70 md:block" />
        <Fleur variante="gypsophile" visible-des="md" class="absolute bottom-10 -left-14 -z-10 hidden w-48 opacity-70 md:block" />

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
                <p class="texte-courant mt-4">Je vous rappelle généralement dans l’heure. À très vite !</p>
                <div class="mt-8 flex flex-wrap justify-center gap-4">
                    <Bouton :href="route('portfolio')" variante="contour">Voir le portfolio</Bouton>
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
                    <Champ id="prenom" v-model="form.prenom" libelle="Prénom" obligatoire :erreur="form.errors.prenom" type="text" autocomplete="given-name" placeholder="Ex. : Léa" />
                    <Champ id="nom" v-model="form.nom" libelle="Nom" obligatoire :erreur="form.errors.nom" type="text" autocomplete="family-name" placeholder="Ex. : Martin" />
                    <Champ
                        id="telephone"
                        v-model="form.telephone"
                        libelle="Téléphone"
                        obligatoire
                        :erreur="form.errors.telephone"
                        type="tel"
                        autocomplete="tel"
                        placeholder="Ex. : 06 12 34 56 78"
                    />
                </div>

                <fieldset class="mt-8" :aria-describedby="form.errors.seances ? 'erreur-seances' : undefined">
                    <legend :class="etiquette">Quel type de séance ? <span class="text-rouge" aria-hidden="true">*</span><span class="sr-only">(obligatoire)</span></legend>
                    <div class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <label v-for="type in types" :key="type.cle" class="relative cursor-pointer">
                            <input
                                :id="`seance-${type.cle}`"
                                v-model="form.seances"
                                type="checkbox"
                                :value="type.cle"
                                class="peer sr-only"
                                :aria-invalid="form.errors.seances ? 'true' : undefined"
                            />
                            <span
                                class="flex flex-col items-center gap-2 rounded-xl border bg-creme-100/70 px-2 py-4 text-center text-[0.95rem] text-cacao-700 transition-colors peer-checked:border-cacao-700 peer-checked:bg-poudre-100 peer-checked:text-cacao-900 peer-focus-visible:ring-2 peer-focus-visible:ring-or-400"
                                :class="form.errors.seances ? 'border-rouge' : 'border-creme-300 hover:border-or-400'"
                            >
                                <Icone :nom="type.icone" class="size-8" />
                                {{ type.nom }}
                            </span>
                            <Icone nom="check" class="absolute top-2 right-2 size-4 text-brique opacity-0 transition-opacity peer-checked:opacity-100" />
                        </label>
                    </div>
                    <p v-if="form.errors.seances" id="erreur-seances" class="mt-2 text-sm text-rouge">{{ form.errors.seances }}</p>
                </fieldset>

                <div class="mt-8 grid gap-6 sm:grid-cols-[minmax(0,0.6fr)_minmax(0,1fr)]">
                    <Champ
                        id="nombre_personnes"
                        v-model="form.nombre_personnes"
                        libelle="Nombre de personnes"
                        :erreur="form.errors.nombre_personnes"
                        type="number"
                        min="1"
                        max="30"
                        inputmode="numeric"
                        placeholder="Ex. : 4"
                    />
                    <Champ id="periode" v-model="form.periode" libelle="Période souhaitée" :erreur="form.errors.periode" type="text" placeholder="Ex. : fin mai, un samedi matin…" />
                </div>

                <div class="mt-8">
                    <Champ
                        id="message"
                        v-model="form.message"
                        libelle="Votre message"
                        obligatoire
                        multiligne
                        :erreur="form.errors.message"
                        rows="6"
                        placeholder="Parlez-moi de vous, de vos envies, de vos questions…"
                    />
                </div>

                <!-- Piège à robots : invisible pour les humains -->
                <div class="absolute -left-[9999px]" aria-hidden="true">
                    <label for="site_web">Ne pas remplir</label>
                    <input id="site_web" v-model="form.site_web" type="text" tabindex="-1" autocomplete="off" />
                </div>

                <div v-if="cleCaptcha" class="mt-10">
                    <Captcha ref="captcha" v-model="form.captcha" :cle="cleCaptcha" :erreur="form.errors.captcha" />
                </div>

                <div class="text-center" :class="cleCaptcha ? 'mt-8' : 'mt-10'">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2.5 rounded-full bg-cacao-800 px-9 py-4 text-[0.75rem] font-normal tracking-[0.24em] text-creme-50 uppercase transition-colors duration-300 hover:bg-brique disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        <Icone nom="coeur" class="size-4" />
                        {{ form.processing ? 'Envoi en cours…' : 'Envoyer ma demande' }}
                    </button>
                    <p class="mt-6 text-[0.95rem] text-cacao-700">Je vous rappelle généralement dans l’heure.</p>
                </div>
            </form>

            <!-- Autres moyens de contact -->
            <div class="mt-8 rounded-2xl border border-creme-300 bg-creme-50 px-6 py-10 text-center sm:px-10">
                <h2 class="text-[1.7rem] leading-tight sm:text-3xl">Me contacter autrement</h2>
                <ul class="mt-6 flex flex-col items-center gap-3 text-[1.02rem] text-cacao-800">
                    <li v-if="site.telephone">
                        <a :href="site.telephoneUrl" class="inline-flex items-center gap-3 transition-colors hover:text-brique">
                            <Icone nom="telephone" class="size-5 shrink-0 text-or-500" /> {{ site.telephone }}
                        </a>
                    </li>
                </ul>
                <ul class="mt-8 flex flex-wrap justify-center gap-3" aria-label="Réseaux sociaux">
                    <li v-for="reseau in site.reseaux" :key="reseau.nom">
                        <Bouton :href="reseau.url" externe variante="contour" :icone="reseau.icone">{{ reseau.nom }}</Bouton>
                    </li>
                </ul>
            </div>
        </div>
    </section>
</template>
