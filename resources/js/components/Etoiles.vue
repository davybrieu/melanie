<script setup>
import { computed } from 'vue';

const props = defineProps({
    // Note sur 5, décimales comprises : 4,6 remplit la 5e étoile à 60 %.
    note: { type: Number, required: true },
});

const etoile = 'M12 2.6l2.9 5.9 6.5.9-4.7 4.6 1.1 6.4L12 17.4l-5.8 3 1.1-6.4-4.7-4.6 6.5-.9z';

const libelle = computed(() => `${String(Math.round(props.note * 10) / 10).replace('.', ',')} sur 5`);
</script>

<!-- Taille des étoiles : celle du texte (text-lg…). Étoiles vides en poudre, pleines en or. -->
<template>
    <span class="relative inline-flex" role="img" :aria-label="libelle">
        <span class="flex text-poudre-300" aria-hidden="true">
            <svg v-for="i in 5" :key="i" viewBox="0 0 24 24" fill="currentColor" class="size-[1.15em] shrink-0"><path :d="etoile" /></svg>
        </span>
        <span class="absolute inset-y-0 left-0 flex overflow-hidden text-or-500" :style="{ width: `${(note / 5) * 100}%` }" aria-hidden="true">
            <svg v-for="i in 5" :key="i" viewBox="0 0 24 24" fill="currentColor" class="size-[1.15em] shrink-0"><path :d="etoile" /></svg>
        </span>
    </span>
</template>
