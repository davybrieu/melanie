<script setup>
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, inject, nextTick, reactive, ref, watch } from 'vue';
import { altPropose, preparerPhoto, verifierAlt } from '../../admin/photos';
import Champ from '../../components/Champ.vue';
import Icone from '../../components/Icone.vue';
import { seances as donneesSeances } from '../../data/seances';
import AdminLayout from '../../layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    // Photos de chaque séance, dans l'ordre du site : { grossesse: [{ id, src, srcset, largeur, hauteur, alt }], … }.
    seances: { type: Object, required: true },
    // Photos du site par emplacement ({ portrait: {…} }) : un emplacement absent est vide.
    emplacements: { type: Object, required: true },
    // Taille maximale d'un envoi accepté par le serveur, en octets.
    tailleMax: { type: Number, required: true },
    // Plus grand côté minimal d'une photo, en pixels.
    coteMinimum: { type: Number, required: true },
});

const route = inject('route');
const page = usePage();

const listeSeances = ['grossesse', 'nouveau-ne', 'famille'].map((cle) => ({ cle, nom: donneesSeances[cle].nom, route: donneesSeances[cle].route }));

// Rôle d'une photo de séance selon son rang (PageController : page séance, accueil, tarifs, portfolio).
function role(index) {
    if (index === 0) return { libelle: 'Photo principale', classes: 'bg-cacao-800 text-creme-50' };
    if (index <= 2) return { libelle: 'Grande photo', classes: 'bg-poudre-200 text-cacao-800' };
    if (index <= 11) return { libelle: 'Galerie', classes: 'bg-creme-200 text-cacao-800' };

    return { libelle: 'Portfolio', classes: 'border border-creme-300 text-cacao-700' };
}

const legende = (seance) => [
    { titre: 'Photo principale (1re)', texte: `en haut de la page ${seance.nom}, sur l’accueil, en couverture du portfolio et sur la page Tarifs. Format portrait, sujet au centre : elle est affichée dans une arche.` },
    { titre: 'Grandes photos (2e et 3e)', texte: `en grand sur la page ${seance.nom} et dans la mosaïque de l’accueil. Format portrait.` },
    { titre: 'Galerie (4e à 12e)', texte: `galerie de la page ${seance.nom}, tous formats.` },
    { titre: 'Toutes', texte: `portfolio ${seance.nom.toLowerCase()}, dans cet ordre.` },
];

// Photos du site : une par emplacement, avec la photo de séance qui la remplace tant qu'elle manque.
const emplacementsSite = computed(() => [
    {
        cle: 'accueil',
        titre: 'Grande photo de l’accueil',
        texte: 'En haut de la page d’accueil, à côté du titre. Paysage ou carré, sujet au centre : elle est recadrée selon l’écran.',
        format: 'aspect-[3/2]',
        repli: props.seances.grossesse.length ? 'En attendant, la photo principale grossesse la remplace.' : null,
    },
    {
        cle: 'portrait',
        titre: 'Votre portrait',
        texte: 'Sur l’accueil (« Bonjour, moi c’est Mélanie ») et sur la page Mon univers. Format portrait, visage au centre : il est affiché dans une arche.',
        format: 'aspect-[4/5]',
        repli: null,
    },
    {
        cle: 'bon-cadeau',
        titre: 'Photo du bon cadeau',
        texte: 'En haut de la page Bon cadeau. Format portrait.',
        format: 'aspect-[4/5]',
        repli: props.seances.famille.length > 1 ? 'En attendant, la 2e photo famille la remplace.' : null,
    },
]);

// Pages où il manque des photos (elles y affichent des emplacements vides).
const aCompleter = computed(() => {
    const liste = emplacementsSite.value
        .filter((emplacement) => !props.emplacements[emplacement.cle] && !emplacement.repli)
        .map((emplacement) => ({ ancre: `emplacement-${emplacement.cle}`, texte: `${emplacement.titre} : aucune photo.` }));

    for (const seance of listeSeances) {
        const nombre = props.seances[seance.cle].length;
        const ancre = `seance-${seance.cle}`;

        if (nombre === 0) {
            liste.push({ ancre, texte: `${seance.nom} : aucune photo. La page ${seance.nom}, son portfolio, l’accueil et les tarifs affichent des emplacements vides.` });
        } else if (nombre < 3) {
            liste.push({ ancre, texte: `${seance.nom} : ${nombre === 2 ? 'il manque une grande photo' : 'il manque les deux grandes photos'} (page ${seance.nom} et mosaïque de l’accueil).` });
        } else if (nombre === 3) {
            liste.push({ ancre, texte: `${seance.nom} : la galerie de la page ${seance.nom} est vide (jusqu’à 9 photos).` });
        }
    }

    return liste;
});

// Textes alternatifs identiques : chaque photo doit avoir le sien.
const normaliser = (texte) =>
    texte
        .normalize('NFD')
        .replace(/\p{Diacritic}/gu, '')
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, ' ')
        .trim();

const enDouble = computed(() => {
    const compte = new Map();

    for (const photo of [...Object.values(props.emplacements), ...listeSeances.flatMap((seance) => props.seances[seance.cle])]) {
        compte.set(normaliser(photo.alt), (compte.get(normaliser(photo.alt)) ?? 0) + 1);
    }

    return new Set([...compte].filter(([, nombre]) => nombre > 1).map(([alt]) => alt));
});

const estEnDouble = (photo) => enDouble.value.has(normaliser(photo.alt));

const conseilAlt =
    'Décrivez ce que montre la photo, comme à quelqu’un qui ne la voit pas : qui, quoi, où. Google (Google Images) et les lecteurs d’écran lisent ce texte, et le nom de la photo en est tiré. Un texte différent pour chaque photo, sans liste de mots-clés ni « photo de… ».';
const exempleAlt = 'Ex. : Future maman en robe fluide au coucher du soleil, au bord d’un lac près de Dijon';

// Message après une action : celui du serveur, ou le bilan d'un envoi.
const banniere = ref(null);
watch(
    () => page.flash?.photos,
    (texte) => texte && (banniere.value = texte),
    { immediate: true },
);

// Ajout : les photos choisies sont décrites une à une, réduites dans le navigateur, puis envoyées
// l'une après l'autre (une par requête, sous la taille qu'accepte le serveur).
const selecteur = ref(null);
const dialogueEnvoi = ref(null);
const envoi = reactive({ dossier: null, emplacement: null, titre: '', lignes: [], enCours: false });

const titreEnvoi = computed(() => (envoi.emplacement ? `${envoi.titre} : nouvelle photo` : `Ajouter des photos : ${envoi.titre}`));
const lignesAEnvoyer = computed(() => envoi.lignes.filter((ligne) => ligne.etat !== 'ok'));

function choisir(dossier, emplacement, titre) {
    Object.assign(envoi, { dossier, emplacement, titre });
    selecteur.value.multiple = !emplacement;
    selecteur.value.click();
}

function fichiersChoisis(evenement) {
    const fichiers = [...evenement.target.files];
    evenement.target.value = '';

    if (!fichiers.length) return;

    viderEnvoi();
    envoi.lignes = fichiers.map((fichier, i) => ({
        cle: `${Date.now()}-${i}`,
        fichier,
        apercu: URL.createObjectURL(fichier),
        alt: altPropose(fichier.name),
        erreurs: {},
        etat: 'attente',
        progression: 0,
    }));
    dialogueEnvoi.value.showModal();
    nextTick(() => dialogueEnvoi.value.querySelector('textarea')?.focus());
}

function retirer(ligne) {
    URL.revokeObjectURL(ligne.apercu);
    envoi.lignes = envoi.lignes.filter((autre) => autre !== ligne);

    if (!lignesAEnvoyer.value.length) terminer();
}

// Plus rien à envoyer : la fenêtre se ferme sur le bilan des photos ajoutées.
function terminer() {
    const ajoutees = envoi.lignes.filter((ligne) => ligne.etat === 'ok').length;

    dialogueEnvoi.value.close();

    if (ajoutees) {
        banniere.value = ajoutees > 1 ? `Les ${ajoutees} photos sont ajoutées.` : 'La photo est ajoutée.';
    }
}

function viderEnvoi() {
    envoi.lignes.forEach((ligne) => URL.revokeObjectURL(ligne.apercu));
    envoi.lignes = [];
}

const envoyerUne = (ligne, photo) =>
    new Promise((resoudre) => {
        router.post(
            '/admin/photos',
            { dossier: envoi.dossier, emplacement: envoi.emplacement, alt: ligne.alt, photo },
            {
                preserveScroll: true,
                preserveState: true,
                forceFormData: true,
                onProgress: (progression) => (ligne.progression = Math.round(progression?.percentage ?? 0)),
                onSuccess: () => (ligne.etat = 'ok'),
                onError: (erreurs) => {
                    ligne.erreurs = erreurs;
                    ligne.etat = 'erreur';
                },
                onFinish: () => resoudre(),
            },
        );
    });

async function envoyer() {
    for (const ligne of lignesAEnvoyer.value) {
        const erreur = verifierAlt(ligne.alt);
        ligne.erreurs = erreur ? { alt: erreur } : {};
        ligne.etat = erreur ? 'erreur' : 'attente';
    }

    if (lignesAEnvoyer.value.some((ligne) => ligne.etat === 'erreur')) {
        return focaliserErreur();
    }

    envoi.enCours = true;

    for (const ligne of lignesAEnvoyer.value) {
        ligne.etat = 'preparation';
        const photo = await preparerPhoto(ligne.fichier, props.tailleMax, props.coteMinimum);
        ligne.etat = 'envoi';
        await envoyerUne(ligne, photo);

        // Réponse inattendue (connexion coupée…) : ni succès ni erreur de validation.
        if (ligne.etat === 'envoi') {
            ligne.erreurs = { photo: 'L’envoi n’a pas abouti : vérifiez votre connexion, puis réessayez.' };
            ligne.etat = 'erreur';
        }
    }

    envoi.enCours = false;

    if (lignesAEnvoyer.value.length) {
        focaliserErreur();
    } else {
        terminer();
    }
}

function focaliserErreur() {
    nextTick(() => dialogueEnvoi.value?.querySelector('[aria-invalid="true"], [data-erreur]')?.focus());
}

const etatLigne = (ligne) =>
    ({
        preparation: 'Préparation de la photo…',
        envoi: `Envoi… ${ligne.progression} %`,
        ok: 'Photo ajoutée',
    })[ligne.etat] ?? null;

// Texte alternatif d'une photo déjà ajoutée.
const dialogueEdition = ref(null);
const edition = reactive({ photo: null, alt: '', erreur: null, enCours: false });

function modifier(photo) {
    Object.assign(edition, { photo, alt: photo.alt, erreur: null });
    dialogueEdition.value.showModal();
    nextTick(() => document.getElementById('alt-edition')?.focus());
}

function enregistrer() {
    edition.erreur = verifierAlt(edition.alt);

    if (edition.erreur) {
        return nextTick(() => document.getElementById('alt-edition')?.focus());
    }

    router.patch(
        `/admin/photos/${edition.photo.id}`,
        { alt: edition.alt },
        {
            preserveScroll: true,
            onStart: () => (edition.enCours = true),
            onFinish: () => (edition.enCours = false),
            onSuccess: () => dialogueEdition.value.close(),
            onError: (erreurs) => (edition.erreur = erreurs.alt ?? 'Le texte n’a pas pu être enregistré.'),
        },
    );
}

// Ordre et suppression. Adresses exclues de Ziggy, comme toutes celles de l'espace de gestion.
const enCours = ref(null);
const suivre = (photo) => ({ preserveScroll: true, onStart: () => (enCours.value = photo.id), onFinish: () => (enCours.value = null) });

const deplacer = (photo, sens) => router.patch(`/admin/photos/${photo.id}/position`, { sens }, suivre(photo));

function supprimer(photo) {
    if (window.confirm(`Supprimer cette photo du site ?\n\n« ${photo.alt} »`)) {
        router.delete(`/admin/photos/${photo.id}`, suivre(photo));
    }
}

// Actions sur une photo : icônes seules, nommées par aria-label et par title (bulle au survol).
const boutonIcone = 'grid size-9 shrink-0 cursor-pointer place-items-center rounded-full border transition-colors disabled:cursor-not-allowed disabled:opacity-40';
const boutonNeutre = `${boutonIcone} border-cacao-800/30 text-cacao-800 enabled:hover:border-cacao-800 enabled:hover:bg-cacao-800 enabled:hover:text-creme-50`;
const boutonRouge = `${boutonIcone} border-rouge/40 text-rouge enabled:hover:border-rouge enabled:hover:bg-rouge enabled:hover:text-creme-50`;
const boutonTexte = 'inline-flex cursor-pointer items-center justify-center gap-2.5 rounded-full px-6 py-3 text-[0.72rem] tracking-[0.24em] uppercase transition-colors disabled:cursor-wait disabled:opacity-60';
const boutonPlein = `${boutonTexte} bg-cacao-800 text-creme-50 hover:bg-brique`;
const boutonContour = `${boutonTexte} border border-cacao-800/45 text-cacao-800 enabled:hover:border-cacao-800 enabled:hover:bg-cacao-800 enabled:hover:text-creme-50`;
const tuileVide =
    'flex w-full cursor-pointer flex-col items-center justify-center gap-2 rounded-2xl border-2 border-dashed border-taupe-400 bg-creme-100 p-5 text-center text-cacao-800 transition-colors hover:border-cacao-800 hover:bg-creme-50';
const lien = 'text-cacao-800 underline decoration-or-400 underline-offset-4 transition-colors hover:text-brique';
const dialogue = 'm-auto max-h-[92svh] w-[min(44rem,calc(100%-2rem))] overflow-y-auto rounded-2xl bg-creme-50 p-0 text-cacao-800 shadow-2xl backdrop:bg-cacao-900/60';
</script>

<template>
    <Head title="Photos | Mélanie Photographie" />

    <!-- Sélecteur de fichiers, ouvert par les boutons d'ajout -->
    <input ref="selecteur" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only" tabindex="-1" aria-hidden="true" @change="fichiersChoisis" />

    <div class="conteneur pt-10 pb-20 sm:pt-14">
        <div v-if="banniere" class="mb-10 flex items-center gap-3 rounded-xl bg-poudre-100 px-5 py-4 text-cacao-800" role="status">
            <Icone nom="check" class="size-5 shrink-0 text-brique" />
            <span>{{ banniere }}</span>
        </div>

        <h1 class="text-[1.9rem] leading-tight">Photos</h1>
        <p class="mt-3 max-w-3xl leading-relaxed text-cacao-700">
            Ajoutez vos photos aux pages du site. Chacune a besoin d’un texte alternatif, qui la décrit à Google et aux personnes qui ne la voient pas. Le site
            crée lui-même les versions optimisées pour chaque écran (WebP, plusieurs tailles), sans les données de localisation de votre téléphone.
        </p>

        <!-- Pages où il manque des photos -->
        <section
            class="mt-8 rounded-2xl border p-5 sm:p-6"
            :class="aCompleter.length || enDouble.size ? 'border-or-400 bg-creme-50' : 'border-creme-300 bg-creme-50'"
            aria-labelledby="titre-a-completer"
        >
            <h2 id="titre-a-completer" class="flex items-center gap-3 text-xl leading-snug">
                <Icone :nom="aCompleter.length ? 'image' : 'check'" class="size-6 shrink-0 text-or-500" />
                {{ aCompleter.length ? 'Pages où il manque des photos' : 'Toutes les pages ont leurs photos' }}
            </h2>
            <ul v-if="aCompleter.length" class="mt-4 space-y-2 text-[0.95rem]">
                <li v-for="manque in aCompleter" :key="manque.ancre + manque.texte" class="flex gap-2">
                    <span class="text-or-500" aria-hidden="true">•</span>
                    <a :href="`#${manque.ancre}`" :class="lien">{{ manque.texte }}</a>
                </li>
            </ul>
            <p v-if="enDouble.size" class="mt-4 text-[0.95rem] text-cacao-700">
                Certaines photos ont le même texte alternatif qu’une autre (signalées en rouge) : décrivez chacune à sa façon.
            </p>
        </section>

        <!-- Photos du site : accueil, portrait, bon cadeau -->
        <section id="site" class="mt-14 scroll-mt-6" aria-labelledby="titre-site">
            <h2 id="titre-site" class="text-[1.6rem] leading-tight">Accueil, portrait et bon cadeau</h2>
            <ul class="mt-6 grid gap-5 lg:grid-cols-3">
                <li
                    v-for="emplacement in emplacementsSite"
                    :id="`emplacement-${emplacement.cle}`"
                    :key="emplacement.cle"
                    class="flex scroll-mt-6 flex-col rounded-2xl border border-creme-300 bg-creme-50 p-5"
                >
                    <h3 class="text-xl">{{ emplacement.titre }}</h3>
                    <p class="mt-1 text-[0.92rem] leading-relaxed text-cacao-700">{{ emplacement.texte }}</p>

                    <template v-if="emplacements[emplacement.cle]">
                        <img
                            :src="emplacements[emplacement.cle].src"
                            :srcset="emplacements[emplacement.cle].srcset"
                            sizes="(min-width: 1024px) 28vw, 90vw"
                            :width="emplacements[emplacement.cle].largeur"
                            :height="emplacements[emplacement.cle].hauteur"
                            :alt="emplacements[emplacement.cle].alt"
                            loading="lazy"
                            class="mt-4 w-full rounded-xl bg-creme-200 object-cover"
                            :class="emplacement.format"
                        />
                        <p class="mt-3 text-[0.92rem] leading-snug">{{ emplacements[emplacement.cle].alt }}</p>
                        <p v-if="estEnDouble(emplacements[emplacement.cle])" class="mt-1 text-[0.8rem] text-rouge">Même texte qu’une autre photo</p>
                        <div class="mt-auto flex flex-wrap items-center gap-2 pt-4">
                            <button type="button" :class="boutonContour" @click="choisir('site', emplacement.cle, emplacement.titre)">
                                <Icone nom="image" class="size-4" /> Remplacer
                            </button>
                            <button
                                type="button"
                                :class="boutonNeutre"
                                :aria-label="`Modifier le texte alternatif : ${emplacement.titre}`"
                                title="Modifier le texte alternatif"
                                @click="modifier(emplacements[emplacement.cle])"
                            >
                                <Icone nom="crayon" class="size-5" />
                            </button>
                            <button
                                type="button"
                                :class="boutonRouge"
                                :disabled="enCours === emplacements[emplacement.cle].id"
                                :aria-label="`Supprimer la photo : ${emplacement.titre}`"
                                title="Supprimer"
                                @click="supprimer(emplacements[emplacement.cle])"
                            >
                                <Icone nom="poubelle" class="size-5" />
                            </button>
                        </div>
                    </template>
                    <button v-else type="button" class="mt-4" :class="[tuileVide, emplacement.format]" @click="choisir('site', emplacement.cle, emplacement.titre)">
                        <Icone nom="plus" class="size-7 text-or-500" />
                        <span class="text-[0.72rem] tracking-[0.2em] uppercase">Ajouter une photo</span>
                        <span v-if="emplacement.repli" class="text-[0.85rem] text-cacao-700">{{ emplacement.repli }}</span>
                    </button>
                </li>
            </ul>
        </section>

        <!-- Photos des séances, dans l'ordre du site -->
        <section v-for="seance in listeSeances" :id="`seance-${seance.cle}`" :key="seance.cle" class="mt-16 scroll-mt-6" :aria-labelledby="`titre-${seance.cle}`">
            <div class="flex flex-wrap items-end justify-between gap-4 border-b border-creme-300 pb-4">
                <div>
                    <h2 :id="`titre-${seance.cle}`" class="text-[1.6rem] leading-tight">{{ seance.nom }}</h2>
                    <p class="mt-1 text-[0.9rem] text-cacao-700">
                        {{ seances[seance.cle].length }} {{ seances[seance.cle].length > 1 ? 'photos' : 'photo' }} ·
                        <a :href="route(seance.route)" target="_blank" rel="noopener" :class="lien">Voir la page</a> ·
                        <a :href="route('portfolio.categorie', { categorie: seance.cle })" target="_blank" rel="noopener" :class="lien">Voir le portfolio</a>
                    </p>
                </div>
                <button type="button" :class="boutonPlein" @click="choisir(seance.cle, null, seance.nom)">
                    <Icone nom="plus" class="size-4" /> Ajouter des photos
                </button>
            </div>

            <dl class="mt-4 grid gap-x-8 gap-y-1.5 text-[0.88rem] leading-relaxed text-cacao-700 lg:grid-cols-2">
                <div v-for="ligne in legende(seance)" :key="ligne.titre">
                    <dt class="inline text-cacao-800">{{ ligne.titre }} : </dt>
                    <dd class="inline">{{ ligne.texte }}</dd>
                </div>
            </dl>

            <ol class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <li
                    v-for="(photo, i) in seances[seance.cle]"
                    :key="photo.id"
                    class="flex gap-4 rounded-2xl border border-creme-300 bg-creme-50 p-3 transition-opacity sm:p-4"
                    :class="{ 'opacity-60': enCours === photo.id }"
                >
                    <img
                        :src="photo.src"
                        :srcset="photo.srcset"
                        sizes="6rem"
                        :width="photo.largeur"
                        :height="photo.hauteur"
                        :alt="photo.alt"
                        loading="lazy"
                        class="aspect-[4/5] w-24 shrink-0 rounded-lg bg-creme-200 object-cover"
                    />
                    <div class="flex min-w-0 flex-1 flex-col">
                        <p>
                            <span class="inline-block rounded-full px-2.5 py-0.5 text-[0.75rem]" :class="role(i).classes">{{ i + 1 }} · {{ role(i).libelle }}</span>
                        </p>
                        <p class="mt-2 line-clamp-3 text-[0.92rem] leading-snug">{{ photo.alt }}</p>
                        <p v-if="estEnDouble(photo)" class="mt-1 text-[0.8rem] text-rouge">Même texte qu’une autre photo</p>
                        <div class="mt-auto flex flex-wrap gap-1.5 pt-3">
                            <button
                                type="button"
                                :class="boutonNeutre"
                                :aria-label="`Modifier le texte alternatif de la photo ${i + 1}`"
                                title="Modifier le texte alternatif"
                                @click="modifier(photo)"
                            >
                                <Icone nom="crayon" class="size-5" />
                            </button>
                            <button
                                type="button"
                                :class="boutonNeutre"
                                :disabled="i === 0 || enCours !== null"
                                :aria-label="`Avancer la photo ${i + 1} d’un rang`"
                                title="Avancer d’un rang"
                                @click="deplacer(photo, 'avant')"
                            >
                                <Icone nom="chevron-gauche" class="size-5" />
                            </button>
                            <button
                                type="button"
                                :class="boutonNeutre"
                                :disabled="i === seances[seance.cle].length - 1 || enCours !== null"
                                :aria-label="`Reculer la photo ${i + 1} d’un rang`"
                                title="Reculer d’un rang"
                                @click="deplacer(photo, 'apres')"
                            >
                                <Icone nom="chevron-droit" class="size-5" />
                            </button>
                            <button
                                v-if="i > 0"
                                type="button"
                                :class="boutonNeutre"
                                :disabled="enCours !== null"
                                :aria-label="`Faire de la photo ${i + 1} la photo principale`"
                                title="En faire la photo principale"
                                @click="deplacer(photo, 'premiere')"
                            >
                                <Icone nom="etoile" class="size-5" />
                            </button>
                            <button
                                type="button"
                                :class="boutonRouge"
                                :disabled="enCours !== null"
                                :aria-label="`Supprimer la photo ${i + 1}`"
                                title="Supprimer"
                                @click="supprimer(photo)"
                            >
                                <Icone nom="poubelle" class="size-5" />
                            </button>
                        </div>
                    </div>
                </li>

                <!-- Photo principale et grandes photos manquantes : la prochaine photo ajoutée prend la première place libre -->
                <li v-for="rang in Math.max(0, 3 - seances[seance.cle].length)" :key="`vide-${rang}`">
                    <button type="button" class="h-full min-h-40" :class="tuileVide" @click="choisir(seance.cle, null, seance.nom)">
                        <Icone nom="plus" class="size-6 text-or-500" />
                        <span class="text-[0.72rem] tracking-[0.2em] uppercase">{{ role(seances[seance.cle].length + rang - 1).libelle }} à ajouter</span>
                    </button>
                </li>
            </ol>
        </section>
    </div>

    <!-- Ajout : texte alternatif de chaque photo, puis envoi -->
    <dialog ref="dialogueEnvoi" :class="dialogue" aria-labelledby="titre-envoi" @close="viderEnvoi" @cancel="(evenement) => envoi.enCours && evenement.preventDefault()">
        <form class="p-5 sm:p-7" novalidate @submit.prevent="envoyer">
            <div class="flex items-start justify-between gap-4">
                <h2 id="titre-envoi" class="text-[1.5rem] leading-tight">{{ titreEnvoi }}</h2>
                <button type="button" :class="boutonNeutre" :disabled="envoi.enCours" aria-label="Fermer" title="Fermer" @click="dialogueEnvoi.close()">
                    <Icone nom="fermer" class="size-5" />
                </button>
            </div>
            <p class="mt-3 text-[0.92rem] leading-relaxed text-cacao-700">{{ conseilAlt }}</p>

            <ul class="mt-6 space-y-4">
                <li v-for="ligne in envoi.lignes" :key="ligne.cle" class="flex gap-4 rounded-xl border border-creme-300 bg-creme-100/60 p-4">
                    <img :src="ligne.apercu" :alt="`Aperçu de ${ligne.fichier.name}`" class="aspect-[4/5] w-20 shrink-0 rounded-lg bg-creme-200 object-cover sm:w-24" />
                    <div class="min-w-0 flex-1">
                        <Champ
                            :id="`alt-${ligne.cle}`"
                            v-model="ligne.alt"
                            libelle="Texte alternatif"
                            obligatoire
                            multiligne
                            rows="2"
                            maxlength="125"
                            :placeholder="exempleAlt"
                            :aide="`${ligne.alt.length} / 125 caractères`"
                            :erreur="ligne.erreurs.alt"
                            :disabled="envoi.enCours || ligne.etat === 'ok'"
                            @input="ligne.erreurs.alt && (ligne.erreurs = { ...ligne.erreurs, alt: null })"
                        />
                        <p v-if="ligne.erreurs.photo || ligne.erreurs.emplacement || ligne.erreurs.dossier" class="mt-2 text-sm text-rouge" tabindex="-1" data-erreur>
                            {{ ligne.erreurs.photo ?? ligne.erreurs.emplacement ?? ligne.erreurs.dossier }}
                        </p>
                        <p v-if="etatLigne(ligne)" class="mt-2 flex items-center gap-2 text-sm text-cacao-700" role="status">
                            <Icone v-if="ligne.etat === 'ok'" nom="check" class="size-4 text-brique" />
                            {{ etatLigne(ligne) }}
                        </p>
                        <div v-if="ligne.etat === 'envoi'" class="mt-2 h-1.5 overflow-hidden rounded-full bg-creme-300" aria-hidden="true">
                            <div class="h-full bg-brique transition-[width]" :style="{ width: `${ligne.progression}%` }"></div>
                        </div>
                    </div>
                    <button
                        v-if="!envoi.enCours && ligne.etat !== 'ok'"
                        type="button"
                        :class="boutonNeutre"
                        :aria-label="`Retirer ${ligne.fichier.name}`"
                        title="Retirer cette photo"
                        @click="retirer(ligne)"
                    >
                        <Icone nom="fermer" class="size-4" />
                    </button>
                </li>
            </ul>

            <p class="mt-5 text-[0.85rem] leading-relaxed text-taupe-500">
                Les photos sont réduites avant l’envoi (2400 px au plus) ; il leur faut au moins {{ coteMinimum }} px sur le plus grand côté pour rester nettes.
            </p>
            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <button type="button" :class="boutonContour" :disabled="envoi.enCours" @click="dialogueEnvoi.close()">Annuler</button>
                <button type="submit" :class="boutonPlein" :disabled="envoi.enCours || !lignesAEnvoyer.length">
                    {{ envoi.enCours ? 'Envoi en cours…' : lignesAEnvoyer.length > 1 ? `Ajouter les ${lignesAEnvoyer.length} photos` : 'Ajouter la photo' }}
                </button>
            </div>
        </form>
    </dialog>

    <!-- Texte alternatif d'une photo déjà sur le site -->
    <dialog ref="dialogueEdition" :class="dialogue" aria-labelledby="titre-edition" @cancel="(evenement) => edition.enCours && evenement.preventDefault()">
        <form v-if="edition.photo" class="p-5 sm:p-7" novalidate @submit.prevent="enregistrer">
            <div class="flex items-start justify-between gap-4">
                <h2 id="titre-edition" class="text-[1.5rem] leading-tight">Texte alternatif</h2>
                <button type="button" :class="boutonNeutre" :disabled="edition.enCours" aria-label="Fermer" title="Fermer" @click="dialogueEdition.close()">
                    <Icone nom="fermer" class="size-5" />
                </button>
            </div>
            <img
                :src="edition.photo.src"
                :srcset="edition.photo.srcset"
                sizes="(min-width: 640px) 24rem, 60vw"
                :width="edition.photo.largeur"
                :height="edition.photo.hauteur"
                :alt="edition.photo.alt"
                class="mt-5 max-h-[40svh] w-full rounded-xl bg-creme-200 object-contain"
            />
            <p class="mt-4 text-[0.92rem] leading-relaxed text-cacao-700">{{ conseilAlt }}</p>
            <div class="mt-5">
                <Champ
                    id="alt-edition"
                    v-model="edition.alt"
                    libelle="Texte alternatif"
                    obligatoire
                    multiligne
                    rows="3"
                    maxlength="125"
                    :placeholder="exempleAlt"
                    :aide="`${edition.alt.length} / 125 caractères`"
                    :erreur="edition.erreur"
                    @input="edition.erreur = null"
                />
            </div>
            <div class="mt-6 flex flex-wrap justify-end gap-3">
                <button type="button" :class="boutonContour" :disabled="edition.enCours" @click="dialogueEdition.close()">Annuler</button>
                <button type="submit" :class="boutonPlein" :disabled="edition.enCours">{{ edition.enCours ? 'Enregistrement…' : 'Enregistrer' }}</button>
            </div>
        </form>
    </dialog>
</template>
