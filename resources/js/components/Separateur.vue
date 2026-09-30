<script setup>
import Icone from './Icone.vue';

defineProps({
    icone: { type: String, default: 'coeur' },
});
</script>

<template>
    <!-- Ligne dorée interrompue par une icône, comme sous le logo. -->
    <div class="flex items-center justify-center gap-4 text-or-500" aria-hidden="true">
        <span class="h-px w-14 bg-linear-to-r from-transparent to-or-400 sm:w-20"></span>
        <Icone :nom="icone" class="size-5" />
        <span class="h-px w-14 bg-linear-to-l from-transparent to-or-400 sm:w-20"></span>
    </div>
</template>
