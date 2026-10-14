<script setup>
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import Etoiles from '../../components/Etoiles.vue';
import Icone from '../../components/Icone.vue';
import { useSite } from '../../composables/useSite';
import AdminLayout from '../../layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const props = defineProps({
    // Accès à la fiche Google configuré (GOOGLE_BUSINESS_* du .env) : sans lui, aucun avis n'est importé.
    configure: { type: Boolean, required: true },
    // { note, noteTexte, nombre, actualiseLe } ; null avant le premier import.
    fiche: { type: Object, default: null },
    // [{ id, auteur, note, texte, date, affiche }], les plus récents d'abord.
    avis: { type: Array, required: true },
});

const site = useSite();

// L'interrupteur change d'état tout de suite (mise à jour optimiste d'Inertia, annulée si
// l'enregistrement échoue). Adresse exclue de Ziggy, comme toutes celles de l'espace de gestion.
function basculer(avis) {
    const affiche = !avis.affiche;

    router.patch(
        `/admin/avis/${avis.id}`,
        { affiche },
        {
            preserveScroll: true,
            optimistic: (actuelles) => ({ avis: actuelles.avis.map((autre) => (autre.id === avis.id ? { ...autre, affiche } : autre)) }),
        },
    );
}

const nombreAffiches = computed(() => props.avis.filter((avis) => avis.affiche).length);

// Google compte aussi les avis sans commentaire (note seule), qui ne sont pas gardés.
const sansCommentaire = computed(() => (props.fiche ? Math.max(0, props.fiche.nombre - props.avis.length) : 0));

const filtre = ref('tous');

const filtres = computed(() => [
    { cle: 'tous', libelle: 'Tous', nombre: props.avis.length },
    { cle: 'affiches', libelle: 'Affichés', nombre: nombreAffiches.value },
    { cle: 'masques', libelle: 'Masqués', nombre: props.avis.length - nombreAffiches.value },
]);

const avisFiltres = computed(() => props.avis.filter((avis) => filtre.value === 'tous' || avis.affiche === (filtre.value === 'affiches')));

// Les avis longs sont coupés ; « Lire la suite » les déplie.
const LONGUEUR_COUPEE = 300;
const deplies = ref([]);

function deplier(avis) {
    deplies.value = deplies.value.includes(avis.id) ? deplies.value.filter((id) => id !== avis.id) : [...deplies.value, avis.id];
}
</script>

<template>
    <Head title="Avis Google | Mélanie Photographie" />

    <div class="conteneur pt-10 sm:pt-14">
        <h1 class="text-[1.9rem] leading-tight">Avis Google</h1>
        <p class="mt-3 max-w-2xl leading-relaxed text-cacao-700">
            Choisissez les avis affichés sur la page d’accueil. Un nouvel avis y apparaît de lui-même : masquez-le ici si vous ne souhaitez pas le montrer. Les avis sans
            commentaire (note seule) ne sont pas listés.
        </p>

        <!-- Note de la fiche -->
        <div v-if="fiche" class="mt-8 flex flex-wrap items-center gap-x-8 gap-y-4 rounded-2xl border border-creme-300 bg-creme-50 p-5 sm:p-6">
            <p v-if="fiche.note !== null" class="flex items-center gap-3">
                <span class="font-serif text-[2.4rem] leading-none text-cacao-800">{{ fiche.noteTexte }}</span>
                <Etoiles :note="fiche.note" class="text-xl" />
            </p>
            <div class="min-w-0 flex-1 basis-60">
                <p class="text-cacao-800">
                    {{ fiche.nombre }} avis sur Google<template v-if="sansCommentaire">, dont {{ sansCommentaire }} sans commentaire</template>
                    · {{ nombreAffiches }} {{ nombreAffiches > 1 ? 'affichés' : 'affiché' }} sur le site
                </p>
                <p class="mt-0.5 text-sm text-taupe-500">Actualisés chaque matin ; dernière mise à jour le {{ fiche.actualiseLe }}.</p>
            </div>
            <a
                v-if="site.googleUrl"
                :href="site.googleUrl"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-2 text-[0.95rem] text-cacao-800 underline decoration-or-400 underline-offset-4 transition-colors hover:decoration-cacao-800"
            >
                <Icone nom="google" class="size-4" />
                Voir la fiche Google
            </a>
        </div>
        <p v-else class="mt-8 rounded-2xl border border-dashed border-creme-300 px-6 py-10 text-center text-cacao-700">
            {{
                configure
                    ? 'Les avis arrivent avec la mise à jour de chaque matin, à 6 h.'
                    : 'L’accès à la fiche Google n’est pas configuré : aucun avis n’est importé.'
            }}
        </p>

        <section v-if="fiche" class="mt-12" aria-labelledby="titre-liste">
            <h2 id="titre-liste" class="text-[1.6rem] leading-tight">Avis avec un commentaire</h2>

            <div v-if="avis.length" class="mt-5 flex flex-wrap gap-2" role="group" aria-label="Filtrer les avis">
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

            <ul v-if="avisFiltres.length" class="mt-6 grid gap-4 lg:grid-cols-2">
                <li
                    v-for="avis in avisFiltres"
                    :key="avis.id"
                    class="flex flex-col rounded-2xl border p-5 transition-colors sm:p-6"
                    :class="avis.affiche ? 'border-creme-300 bg-creme-50' : 'border-dashed border-taupe-400 bg-creme-100'"
                >
                    <p class="flex flex-wrap items-center gap-x-3 gap-y-1">
                        <Etoiles :note="avis.note" class="text-lg" />
                        <span class="text-sm text-taupe-500">{{ avis.date }}</span>
                    </p>
                    <p class="mt-3 flex items-center justify-between gap-3">
                        <span class="min-w-0 font-serif text-[1.3rem] leading-snug break-words text-cacao-800">{{ avis.auteur }}</span>
                        <span v-if="!avis.affiche" class="shrink-0 rounded-full border border-taupe-400 px-3 py-0.5 text-[0.8rem] text-cacao-700">Masqué</span>
                    </p>
                    <p
                        class="mt-2 text-[0.97rem] leading-relaxed whitespace-pre-line text-cacao-700"
                        :class="{ 'line-clamp-5': avis.texte.length > LONGUEUR_COUPEE && !deplies.includes(avis.id) }"
                    >
                        {{ avis.texte }}
                    </p>
                    <button
                        v-if="avis.texte.length > LONGUEUR_COUPEE"
                        type="button"
                        class="mt-2 cursor-pointer self-start text-sm text-cacao-800 underline decoration-or-400 underline-offset-4 hover:decoration-cacao-800"
                        :aria-expanded="deplies.includes(avis.id)"
                        @click="deplier(avis)"
                    >
                        {{ deplies.includes(avis.id) ? 'Réduire' : 'Lire la suite' }}
                    </button>

                    <!-- Interrupteur : toute la ligne est cliquable ; l'auteur complète son nom pour les lecteurs d'écran. -->
                    <div class="mt-auto pt-5">
                        <button
                            type="button"
                            role="switch"
                            :aria-checked="avis.affiche"
                            class="flex w-full cursor-pointer items-center justify-between gap-4 border-t border-creme-300 pt-4 text-left text-[0.95rem] text-cacao-800"
                            @click="basculer(avis)"
                        >
                            <span>Afficher sur le site<span class="sr-only"> l’avis de {{ avis.auteur }}</span></span>
                            <span
                                class="inline-flex h-7 w-12 shrink-0 items-center rounded-full border-2 px-0.5 transition-colors duration-200"
                                :class="avis.affiche ? 'border-brique bg-brique' : 'border-taupe-500 bg-creme-50'"
                                aria-hidden="true"
                            >
                                <span
                                    class="rounded-full transition-all duration-200"
                                    :class="avis.affiche ? 'size-5 translate-x-5 bg-creme-50' : 'size-4 translate-x-0.5 bg-taupe-500'"
                                ></span>
                            </span>
                        </button>
                    </div>
                </li>
            </ul>
            <p v-else class="mt-6 rounded-2xl border border-dashed border-creme-300 px-6 py-10 text-center text-cacao-700">
                {{ avis.length ? 'Aucun avis dans cette catégorie.' : 'Aucun avis avec un commentaire pour l’instant.' }}
            </p>
        </section>
    </div>
</template>
