<script setup>
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue';
import { LARGEUR, ajouterMois, dateLongue, dessinerBonCadeau, telechargerBonCadeau } from '../../admin/bonCadeau';
import Champ from '../../components/Champ.vue';
import Icone from '../../components/Icone.vue';
import { useSite } from '../../composables/useSite';
import { validiteBonCadeauMois } from '../../data/conditions';
import { seances } from '../../data/seances';
import AdminLayout from '../../layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    bons: { type: Array, required: true },
    // Date du jour en France (AAAA-MM-JJ) : date d'émission proposée.
    aujourdhui: { type: String, required: true },
});

const page = usePage();
const site = useSite();

// Adresses de l'espace de gestion : elles sont exclues de Ziggy (config/ziggy.php).
const adresses = {
    bons: '/admin/bons-cadeaux',
    bon: (bon) => `/admin/bons-cadeaux/${bon.id}`,
    utilisation: (bon) => `/admin/bons-cadeaux/${bon.id}/utilisation`,
};

const choixSeances = ['grossesse', 'nouveau-ne', 'famille'].map((cle) => seances[cle]);

const longueurMessage = 200;

const nouveauBon = () => ({
    beneficiaire: '',
    offert_par: '',
    seance: '',
    message: '',
    emis_le: props.aujourdhui,
    valable_jusqu_au: ajouterMois(props.aujourdhui, validiteBonCadeauMois),
});

const form = useForm(nouveauBon());

// Bon en cours de modification ; null pour la création d'un nouveau bon.
const modifie = ref(null);

// Une erreur disparaît dès que le champ concerné est modifié.
watch(
    () => ({ ...form.data() }),
    (actuel, precedent) => Object.keys(actuel).filter((cle) => actuel[cle] !== precedent[cle]).forEach((cle) => form.clearErrors(cle)),
);

// Nouvelle date d'émission : la fin de validité suit, sauf si elle a été choisie à la main.
function changerEmission(date) {
    if (date && form.emis_le && form.valable_jusqu_au === ajouterMois(form.emis_le, validiteBonCadeauMois)) {
        form.valable_jusqu_au = ajouterMois(date, validiteBonCadeauMois);
    }

    form.emis_le = date;
}

function remplir(valeurs) {
    form.defaults(valeurs);
    form.reset();
    form.clearErrors();
}

function modifier(bon) {
    modifie.value = bon;
    remplir({
        beneficiaire: bon.beneficiaire,
        offert_par: bon.offert_par ?? '',
        seance: bon.seance,
        message: bon.message ?? '',
        emis_le: bon.emis_le,
        valable_jusqu_au: bon.valable_jusqu_au,
    });
    document.getElementById('formulaire')?.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function nouveau() {
    modifie.value = null;
    remplir(nouveauBon());
}

const ordreChamps = ['beneficiaire', 'offert_par', 'seance', 'message', 'emis_le', 'valable_jusqu_au'];

function enregistrer() {
    const options = {
        preserveScroll: true,
        onSuccess: () => nouveau(),
        onError: (erreurs) => {
            const champ = ordreChamps.find((cle) => erreurs[cle]);
            nextTick(() => document.getElementById(champ === 'seance' ? `seance-${choixSeances[0].cle}` : champ)?.focus());
        },
    };

    if (modifie.value) {
        form.put(adresses.bon(modifie.value), options);
    } else {
        form.post(adresses.bons, options);
    }
}

function basculerUtilisation(bon) {
    router.patch(adresses.utilisation(bon), { utilise: bon.statut !== 'utilise' }, { preserveScroll: true });
}

function supprimer(bon) {
    if (!window.confirm(`Supprimer le bon cadeau ${bon.code} (${bon.beneficiaire}) ? Il disparaîtra de la liste, définitivement.`)) {
        return;
    }

    router.delete(adresses.bon(bon), {
        preserveScroll: true,
        onSuccess: () => modifie.value?.id === bon.id && nouveau(),
    });
}

// Image à imprimer : dessinée dans le navigateur, puis téléchargée.
const enTelechargement = ref(null);

async function telecharger(bon) {
    enTelechargement.value = bon.id;

    try {
        await telechargerBonCadeau(bon, site.value);
    } finally {
        enTelechargement.value = null;
    }
}

// Message après une création, une modification ou une suppression.
const flash = computed(() => page.flash?.bonCadeau ?? null);
const bonFlash = computed(() => (flash.value?.id ? (props.bons.find((bon) => bon.id === flash.value.id) ?? null) : null));
const messagesFlash = { cree: 'est créé', modifie: 'est modifié', supprime: 'est supprimé' };

// Aperçu, redessiné à chaque modification du formulaire. Il est dessiné à sa taille d'affichage
// (écrans haute densité compris) : plus net que l'image d'impression réduite par le navigateur.
const apercu = ref(null);
const bonApercu = computed(() => ({ ...form.data(), code: modifie.value?.code ?? '' }));
let dessinPrevu = false;
let observateur = null;

function redessiner() {
    if (dessinPrevu) return;
    dessinPrevu = true;

    requestAnimationFrame(async () => {
        dessinPrevu = false;
        const canvas = apercu.value;
        const echelle = canvas ? Math.min(1, (canvas.clientWidth * window.devicePixelRatio) / LARGEUR) : 0;

        if (echelle > 0 && bonApercu.value.valable_jusqu_au) {
            await dessinerBonCadeau(canvas, bonApercu.value, site.value, echelle);
        }
    });
}

watch(bonApercu, redessiner);

onMounted(() => {
    observateur = new ResizeObserver(redessiner);
    observateur.observe(apercu.value);
});

onBeforeUnmount(() => observateur?.disconnect());

// Liste : filtre par statut et recherche par nom ou numéro.
const statuts = {
    valide: { libelle: 'Valide', filtre: 'Valides', classes: 'border-or-400 bg-creme-50 text-cacao-800' },
    utilise: { libelle: 'Utilisé', filtre: 'Utilisés', classes: 'border-creme-300 bg-creme-200 text-cacao-700' },
    expire: { libelle: 'Expiré', filtre: 'Expirés', classes: 'border-rouge/50 bg-creme-50 text-rouge' },
};

const filtre = ref('tous');
const recherche = ref('');

const filtres = computed(() => [
    { cle: 'tous', libelle: 'Tous', nombre: props.bons.length },
    ...Object.entries(statuts).map(([cle, statut]) => ({ cle, libelle: statut.filtre, nombre: props.bons.filter((bon) => bon.statut === cle).length })),
]);

const sansAccents = (texte) => (texte ?? '').normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase();

const bonsAffiches = computed(() => {
    const terme = sansAccents(recherche.value.trim());

    return props.bons.filter(
        (bon) => (filtre.value === 'tous' || bon.statut === filtre.value) && (!terme || [bon.code, bon.beneficiaire, bon.offert_par].some((valeur) => sansAccents(valeur).includes(terme))),
    );
});

const etiquette = 'text-[0.72rem] font-normal tracking-[0.2em] text-cacao-700 uppercase';
const boutonAction =
    'inline-flex cursor-pointer items-center gap-2 rounded-full border border-cacao-800/30 px-4 py-2 text-[0.68rem] tracking-[0.18em] text-cacao-800 uppercase transition-colors hover:border-cacao-800 hover:bg-cacao-800 hover:text-creme-50 disabled:opacity-60';
</script>

<template>
    <Head title="Bons cadeaux | Mélanie Photographie" />

    <div class="conteneur pt-10 sm:pt-14">
        <div v-if="flash" class="mb-10 flex flex-wrap items-center justify-between gap-4 rounded-xl bg-poudre-100 px-5 py-4 text-cacao-800" role="status">
            <p class="flex items-center gap-3">
                <Icone nom="check" class="size-5 shrink-0 text-brique" />
                <span>Le bon cadeau {{ flash.code }} {{ messagesFlash[flash.action] }}.</span>
            </p>
            <button v-if="bonFlash" type="button" :class="boutonAction" :disabled="enTelechargement === bonFlash.id" @click="telecharger(bonFlash)">
                {{ enTelechargement === bonFlash.id ? 'Préparation…' : 'Télécharger l’image' }}
            </button>
        </div>

        <!-- Création ou modification, avec l'aperçu de l'image -->
        <section id="formulaire" class="grid scroll-mt-6 gap-10 lg:grid-cols-[minmax(0,1fr)_minmax(0,1.2fr)] lg:items-start" aria-labelledby="titre-formulaire">
            <form class="rounded-2xl border border-creme-300 bg-creme-50 p-6 sm:p-8" novalidate @submit.prevent="enregistrer">
                <h1 id="titre-formulaire" class="text-[1.9rem] leading-tight">
                    {{ modifie ? `Modifier le bon ${modifie.code}` : 'Nouveau bon cadeau' }}
                </h1>

                <div class="mt-7 grid gap-6">
                    <Champ
                        id="beneficiaire"
                        v-model="form.beneficiaire"
                        libelle="Pour"
                        obligatoire
                        :erreur="form.errors.beneficiaire"
                        type="text"
                        maxlength="50"
                        autocomplete="off"
                        placeholder="Ex. : Léa Martin"
                    />
                    <Champ
                        id="offert_par"
                        v-model="form.offert_par"
                        libelle="De la part de"
                        :erreur="form.errors.offert_par"
                        type="text"
                        maxlength="50"
                        autocomplete="off"
                        placeholder="Ex. : Thomas"
                    />

                    <fieldset :aria-describedby="form.errors.seance ? 'erreur-seance' : undefined">
                        <legend :class="etiquette">Séance offerte <span class="text-rouge" aria-hidden="true">*</span><span class="sr-only">(obligatoire)</span></legend>
                        <div class="mt-2 grid grid-cols-3 gap-3">
                            <label v-for="seance in choixSeances" :key="seance.cle" class="relative cursor-pointer">
                                <input
                                    :id="`seance-${seance.cle}`"
                                    v-model="form.seance"
                                    type="radio"
                                    name="seance"
                                    :value="seance.cle"
                                    class="peer sr-only"
                                    :aria-invalid="form.errors.seance ? 'true' : undefined"
                                />
                                <span
                                    class="flex flex-col items-center gap-2 rounded-xl border bg-creme-100/70 px-1 py-4 text-center text-[0.9rem] whitespace-nowrap text-cacao-700 transition-colors sm:text-[0.95rem] peer-checked:border-cacao-700 peer-checked:bg-poudre-100 peer-checked:text-cacao-900 peer-focus-visible:ring-2 peer-focus-visible:ring-or-400"
                                    :class="form.errors.seance ? 'border-rouge' : 'border-creme-300 hover:border-or-400'"
                                >
                                    <Icone :nom="seance.icone" class="size-8" />
                                    {{ seance.nom }}
                                </span>
                                <Icone nom="check" class="absolute top-2 right-2 size-4 text-brique opacity-0 transition-opacity peer-checked:opacity-100" />
                            </label>
                        </div>
                        <p v-if="form.errors.seance" id="erreur-seance" class="mt-2 text-sm text-rouge">{{ form.errors.seance }}</p>
                    </fieldset>

                    <Champ
                        id="message"
                        v-model="form.message"
                        libelle="Message"
                        multiligne
                        rows="3"
                        :maxlength="longueurMessage"
                        :erreur="form.errors.message"
                        :aide="`Imprimé sur le bon : ${longueurMessage - form.message.length} caractères restants.`"
                        placeholder="Ex. : Pour garder un souvenir de ces derniers mois à deux…"
                    />

                    <div class="grid gap-6 sm:grid-cols-2">
                        <Champ
                            id="emis_le"
                            :model-value="form.emis_le"
                            libelle="Émis le"
                            obligatoire
                            :erreur="form.errors.emis_le"
                            type="date"
                            @update:model-value="changerEmission"
                        />
                        <Champ
                            id="valable_jusqu_au"
                            v-model="form.valable_jusqu_au"
                            libelle="Valable jusqu’au"
                            obligatoire
                            :erreur="form.errors.valable_jusqu_au"
                            type="date"
                            :aide="`${validiteBonCadeauMois} mois après l’émission, sauf choix contraire.`"
                        />
                    </div>
                </div>

                <div class="mt-9 flex flex-wrap items-center gap-4">
                    <button
                        type="submit"
                        class="inline-flex cursor-pointer items-center justify-center gap-2.5 rounded-full bg-cacao-800 px-8 py-4 text-[0.72rem] font-normal tracking-[0.24em] text-creme-50 uppercase transition-colors duration-300 hover:bg-brique disabled:opacity-60"
                        :disabled="form.processing"
                    >
                        <Icone nom="cadeau" class="size-4" />
                        {{ form.processing ? 'Enregistrement…' : modifie ? 'Enregistrer les modifications' : 'Créer le bon cadeau' }}
                    </button>
                    <button v-if="modifie" type="button" class="cursor-pointer text-[0.72rem] tracking-[0.2em] text-cacao-700 uppercase underline-offset-4 hover:underline" @click="nouveau">
                        Annuler
                    </button>
                </div>
            </form>

            <div class="lg:sticky lg:top-6">
                <p class="surtitre">Aperçu de l’image à imprimer</p>
                <canvas
                    ref="apercu"
                    width="2480"
                    height="1748"
                    class="mt-4 block h-auto w-full rounded-md bg-creme-50 shadow-[0_18px_40px_-24px_rgba(75,52,40,0.45)]"
                    role="img"
                    :aria-label="`Aperçu du bon cadeau pour ${form.beneficiaire || '…'}`"
                ></canvas>
                <p class="mt-4 text-sm leading-relaxed text-taupe-500">
                    Format A5 paysage (21 × 14,8 cm) en haute définition : choisissez « Taille réelle » à l’impression.
                    <template v-if="!modifie">Le numéro du bon est attribué à sa création.</template>
                </p>
            </div>
        </section>

        <!-- Bons déjà créés -->
        <section class="mt-20" aria-labelledby="titre-liste">
            <div class="flex flex-wrap items-end justify-between gap-5">
                <h2 id="titre-liste" class="text-[1.9rem] leading-tight">Bons cadeaux créés</h2>
                <input
                    v-model="recherche"
                    type="search"
                    class="w-full rounded-full border border-creme-300 bg-creme-50 px-5 py-2.5 text-[0.95rem] text-cacao-800 placeholder:text-taupe-500 focus:border-or-500 focus:ring-2 focus:ring-or-300/40 focus:outline-none sm:w-72"
                    placeholder="Nom ou numéro…"
                    aria-label="Rechercher un bon cadeau par nom ou numéro"
                />
            </div>

            <div class="mt-6 flex flex-wrap gap-2" role="group" aria-label="Filtrer les bons par statut">
                <button
                    v-for="choix in filtres"
                    :key="choix.cle"
                    type="button"
                    class="cursor-pointer rounded-full border px-4 py-2 text-[0.9rem] transition-colors"
                    :class="filtre === choix.cle ? 'border-cacao-800 bg-cacao-800 text-creme-50' : 'border-creme-300 bg-creme-50 text-cacao-700 hover:border-or-400'"
                    :aria-pressed="filtre === choix.cle"
                    @click="filtre = choix.cle"
                >
                    {{ choix.libelle }} <span class="opacity-80">({{ choix.nombre }})</span>
                </button>
            </div>

            <ul v-if="bonsAffiches.length" class="mt-6 grid gap-4">
                <li
                    v-for="bon in bonsAffiches"
                    :key="bon.id"
                    class="flex flex-col gap-5 rounded-2xl border bg-creme-50 p-5 sm:p-6 lg:flex-row lg:items-center lg:justify-between"
                    :class="modifie?.id === bon.id ? 'border-or-500' : 'border-creme-300'"
                >
                    <div class="min-w-0">
                        <p class="flex flex-wrap items-center gap-3">
                            <span class="text-[0.95rem] font-normal tracking-[0.14em] text-cacao-800">{{ bon.code }}</span>
                            <span class="rounded-full border px-3 py-0.5 text-[0.8rem]" :class="statuts[bon.statut].classes">
                                {{ statuts[bon.statut].libelle }}<template v-if="bon.utilise_le"> le {{ dateLongue(bon.utilise_le) }}</template>
                            </span>
                        </p>
                        <p class="mt-2 font-serif text-[1.45rem] leading-snug break-words text-cacao-800">{{ bon.beneficiaire }}</p>
                        <p class="mt-1 text-[0.97rem] text-cacao-700">
                            {{ seances[bon.seance]?.titre ?? bon.seance }}<template v-if="bon.offert_par"> · de la part de {{ bon.offert_par }}</template>
                        </p>
                        <p class="mt-1 text-sm text-taupe-500">Émis le {{ dateLongue(bon.emis_le) }} · valable jusqu’au {{ dateLongue(bon.valable_jusqu_au) }}</p>
                    </div>

                    <div class="flex shrink-0 flex-wrap gap-2 lg:justify-end">
                        <button type="button" :class="boutonAction" :disabled="enTelechargement === bon.id" @click="telecharger(bon)">
                            {{ enTelechargement === bon.id ? 'Préparation…' : 'Télécharger' }}
                        </button>
                        <button type="button" :class="boutonAction" @click="modifier(bon)">Modifier</button>
                        <button type="button" :class="boutonAction" @click="basculerUtilisation(bon)">
                            {{ bon.statut === 'utilise' ? 'Annuler l’utilisation' : 'Marquer utilisé' }}
                        </button>
                        <button
                            type="button"
                            class="inline-flex cursor-pointer items-center rounded-full border border-rouge/40 px-4 py-2 text-[0.68rem] tracking-[0.18em] text-rouge uppercase transition-colors hover:border-rouge hover:bg-rouge hover:text-creme-50"
                            @click="supprimer(bon)"
                        >
                            Supprimer
                        </button>
                    </div>
                </li>
            </ul>
            <p v-else class="mt-6 rounded-2xl border border-dashed border-creme-300 px-6 py-10 text-center text-cacao-700">
                {{ bons.length ? 'Aucun bon ne correspond à cette recherche.' : 'Aucun bon cadeau pour le moment : le premier apparaîtra ici.' }}
            </p>
        </section>
    </div>
</template>
