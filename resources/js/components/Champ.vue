<script setup>
import { computed } from 'vue';

// Champ de formulaire : libellé, saisie (ligne ou zone de texte), aide et message d'erreur.
// En cas d'erreur, la bordure et le message passent en rouge. Les autres attributs
// (type, placeholder, autocomplete, rows, min, @input…) vont directement à la saisie.
defineOptions({ inheritAttrs: false });

const props = defineProps({
    id: { type: String, required: true },
    libelle: { type: String, required: true },
    obligatoire: { type: Boolean, default: false },
    multiligne: { type: Boolean, default: false },
    aide: { type: String, default: null },
    erreur: { type: String, default: null },
});

const valeur = defineModel({ type: [String, Number], default: '' });

const decritPar = computed(() => [props.aide && `aide-${props.id}`, props.erreur && `erreur-${props.id}`].filter(Boolean).join(' ') || undefined);

const classes = computed(() => [
    'mt-2 block w-full rounded-xl border bg-creme-100/70 px-4 py-3 text-[1rem] font-normal text-cacao-800 transition-colors placeholder:font-light placeholder:text-taupe-500 focus:bg-creme-50 focus:ring-2 focus:outline-none',
    props.erreur ? 'border-rouge focus:border-rouge focus:ring-rouge/25' : 'border-creme-300 focus:border-or-500 focus:ring-or-300/40',
]);
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
            :class="classes"
            :aria-invalid="erreur ? 'true' : undefined"
            :aria-describedby="decritPar"
        ></textarea>
        <input
            v-else
            :id="id"
            v-model="valeur"
            v-bind="$attrs"
            :required="obligatoire"
            :class="classes"
            :aria-invalid="erreur ? 'true' : undefined"
            :aria-describedby="decritPar"
        />
        <p v-if="aide" :id="`aide-${id}`" class="mt-2 text-sm text-taupe-500">{{ aide }}</p>
        <p v-if="erreur" :id="`erreur-${id}`" class="mt-1.5 text-sm text-rouge">{{ erreur }}</p>
    </div>
</template>
