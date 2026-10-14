<script setup>
import { computed } from 'vue';
import Icone from './Icone.vue';

// Cases illustrées des types de séance (formulaire de contact, bons cadeaux) : plusieurs choix
// possibles (cases à cocher, modèle en tableau) ou un seul (boutons radio, modèle en texte).
// Les cases s'appellent `${id}-${clé}` (la première reçoit le focus après un envoi refusé) ;
// en cas d'erreur, leur bordure et le message passent en rouge, comme dans Champ.vue.
const props = defineProps({
    id: { type: String, required: true },
    libelle: { type: String, required: true },
    // [{ cle, nom, icone }]
    choix: { type: Array, required: true },
    multiple: { type: Boolean, default: false },
    obligatoire: { type: Boolean, default: false },
    erreur: { type: String, default: null },
});

const valeur = defineModel({ type: [Array, String], default: null });

// Trois choix : une ligne de trois. Quatre : deux colonnes sur mobile, une ligne au-delà.
const grille = computed(() => (props.choix.length === 3 ? 'grid-cols-3' : 'grid-cols-2 sm:grid-cols-4'));
</script>

<template>
    <fieldset :aria-describedby="erreur ? `erreur-${id}` : undefined">
        <legend class="text-[0.72rem] font-normal tracking-[0.2em] text-cacao-700 uppercase">
            {{ libelle }}
            <template v-if="obligatoire"><span class="text-rouge" aria-hidden="true">*</span><span class="sr-only">(obligatoire)</span></template>
        </legend>
        <div class="mt-3 grid gap-3" :class="grille">
            <label v-for="option in choix" :key="option.cle" class="relative cursor-pointer">
                <input
                    :id="`${id}-${option.cle}`"
                    v-model="valeur"
                    :type="multiple ? 'checkbox' : 'radio'"
                    :name="id"
                    :value="option.cle"
                    class="peer sr-only"
                    :aria-invalid="erreur ? 'true' : undefined"
                />
                <span
                    class="flex h-full flex-col items-center gap-2 rounded-xl border bg-creme-100/70 px-1 py-4 text-center text-[0.95rem] text-cacao-700 transition-colors peer-checked:border-cacao-700 peer-checked:bg-poudre-100 peer-checked:text-cacao-900 peer-focus-visible:ring-2 peer-focus-visible:ring-or-400"
                    :class="erreur ? 'border-rouge' : 'border-creme-300 hover:border-or-400'"
                >
                    <Icone :nom="option.icone" class="size-8" />
                    {{ option.nom }}
                </span>
                <Icone nom="check" class="absolute top-2 right-2 size-4 text-brique opacity-0 transition-opacity peer-checked:opacity-100" />
            </label>
        </div>
        <p v-if="erreur" :id="`erreur-${id}`" class="mt-2 text-sm text-rouge">{{ erreur }}</p>
    </fieldset>
</template>
