<script setup>
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import Icone from './Icone.vue';

// Champ de formulaire : libellé, saisie (ligne ou zone de texte), aide et message d'erreur.
// En cas d'erreur, la bordure et le message passent en rouge. Les autres attributs
// (type, placeholder, autocomplete, rows, min, @input…) vont directement à la saisie.
// Avec `revelable` (mot de passe), un bouton en forme d'œil affiche ou masque ce qui est saisi.
defineOptions({ inheritAttrs: false });

const props = defineProps({
    id: { type: String, required: true },
    libelle: { type: String, required: true },
    obligatoire: { type: Boolean, default: false },
    multiligne: { type: Boolean, default: false },
    revelable: { type: Boolean, default: false },
    aide: { type: String, default: null },
    erreur: { type: String, default: null },
});

const valeur = defineModel({ type: [String, Number], default: '' });

const decritPar = computed(() => [props.aide && `aide-${props.id}`, props.erreur && `erreur-${props.id}`].filter(Boolean).join(' ') || undefined);

const classes = computed(() => [
    'block w-full rounded-xl border bg-creme-100/70 px-4 py-3 text-[1rem] font-normal text-cacao-800 transition-colors placeholder:font-light placeholder:text-taupe-500 focus:bg-creme-50 focus:ring-2 focus:outline-none',
    props.erreur ? 'border-rouge focus:border-rouge focus:ring-rouge/25' : 'border-creme-300 focus:border-or-500 focus:ring-or-300/40',
]);

// Mot de passe affiché en clair : il est masqué de nouveau à l'envoi du formulaire, pour que le
// navigateur ne le retienne pas comme un texte ordinaire.
const saisie = ref(null);
const revele = ref(false);
const masquer = () => (revele.value = false);

onMounted(() => props.revelable && saisie.value?.form?.addEventListener('submit', masquer));
onBeforeUnmount(() => saisie.value?.form?.removeEventListener('submit', masquer));
</script>

<template>
    <div>
        <label :for="id" class="text-[0.72rem] font-normal tracking-[0.2em] text-cacao-700 uppercase">
            {{ libelle }} <span v-if="obligatoire" class="text-rouge" aria-hidden="true">*</span>
        </label>
        <textarea
            v-if="multiligne"
            :id="id"
            v-model="valeur"
            v-bind="$attrs"
            :required="obligatoire"
            :class="['mt-2', classes]"
            :aria-invalid="erreur ? 'true' : undefined"
            :aria-describedby="decritPar"
        ></textarea>
        <div v-else class="relative mt-2">
            <!-- :type après v-bind="$attrs" : il l'emporte sur le type reçu quand le mot de passe est affiché -->
            <input
                :id="id"
                ref="saisie"
                v-model="valeur"
                v-bind="$attrs"
                :type="revelable && revele ? 'text' : $attrs.type"
                :required="obligatoire"
                :class="[classes, { 'pr-14 [&::-ms-reveal]:hidden': revelable }]"
                :aria-invalid="erreur ? 'true' : undefined"
                :aria-describedby="decritPar"
            />
            <!-- Libellé fixe et état dans aria-pressed (bouton bascule) ; ::-ms-reveal masque l'œil ajouté par Edge -->
            <button
                v-if="revelable"
                type="button"
                class="absolute inset-y-0 right-0 grid w-12 cursor-pointer place-items-center rounded-r-xl text-taupe-500 transition-colors hover:text-cacao-800"
                :aria-pressed="revele"
                :aria-controls="id"
                aria-label="Afficher le mot de passe"
                title="Afficher le mot de passe"
                @click="revele = !revele"
            >
                <Icone :nom="revele ? 'oeil-barre' : 'oeil'" class="size-5" />
            </button>
        </div>
        <p v-if="aide" :id="`aide-${id}`" class="mt-2 text-sm text-taupe-500">{{ aide }}</p>
        <p v-if="erreur" :id="`erreur-${id}`" class="mt-1.5 text-sm text-rouge">{{ erreur }}</p>
    </div>
</template>
