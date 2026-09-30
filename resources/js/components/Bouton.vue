<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import Icone from './Icone.vue';

const props = defineProps({
    href: { type: String, required: true },
    // plein | contour | lien
    variante: { type: String, default: 'plein' },
    externe: { type: Boolean, default: false },
    icone: { type: String, default: null },
});

// Ancres (#tarif) et liens externes : balise <a> classique, sans navigation Inertia.
const natif = computed(() => props.externe || props.href.startsWith('#'));

const classes = computed(() => {
    if (props.variante === 'lien') {
        return 'group inline-flex items-center gap-2.5 border-b border-or-400/70 pb-1 text-[0.72rem] font-normal tracking-[0.24em] text-cacao-800 uppercase transition-colors hover:border-cacao-800';
    }

    return [
        'inline-flex items-center justify-center gap-2.5 rounded-full px-7 py-3.5 text-[0.72rem] font-normal tracking-[0.24em] uppercase transition-colors duration-300',
        props.variante === 'contour'
            ? 'border border-cacao-800/45 text-cacao-800 hover:border-cacao-800 hover:bg-cacao-800 hover:text-creme-50'
            : 'bg-cacao-800 text-creme-50 hover:bg-brique',
    ];
});
</script>

<template>
    <a v-if="natif" :href="href" :target="externe ? '_blank' : undefined" :rel="externe ? 'noopener' : undefined" :class="classes">
        <Icone v-if="icone" :nom="icone" class="size-4" />
        <slot />
    </a>
    <Link v-else :href="href" :class="classes">
        <Icone v-if="icone" :nom="icone" class="size-4" />
        <slot />
        <Icone v-if="variante === 'lien'" nom="fleche" class="size-4 transition-transform duration-300 group-hover:translate-x-1" />
    </Link>
</template>
