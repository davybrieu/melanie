<script setup>
defineProps({
    etapes: { type: Array, required: true },
});
</script>

<template>
    <ol class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <li v-for="(etape, i) in etapes" :key="etape.titre" class="relative rounded-2xl border border-creme-300 bg-creme-50 p-7">
            <span class="font-script text-[3.4rem] leading-none text-or-500" aria-hidden="true">{{ i + 1 }}</span>
            <h3 class="mt-2 text-xl">{{ etape.titre }}</h3>
            <p class="mt-3 text-[0.97rem] leading-relaxed text-cacao-700">{{ etape.texte }}</p>
        </li>
    </ol>
</template>
