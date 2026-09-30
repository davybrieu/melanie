<script setup>
import Icone from './Icone.vue';
import Pinceau from './Pinceau.vue';

defineProps({
    surtitre: { type: String, default: null },
    titre: { type: String, required: true },
    manuscrit: { type: String, default: null },
    balise: { type: String, default: 'h2' },
    majuscules: { type: Boolean, default: false },
    centre: { type: Boolean, default: true },
    coeurs: { type: Boolean, default: false },
});
</script>

<template>
    <div :class="centre ? 'text-center' : ''">
        <p v-if="surtitre" class="surtitre inline-flex items-center gap-3">
            <Icone v-if="coeurs" nom="coeur" class="size-3.5 text-or-500" />
            {{ surtitre }}
            <Icone v-if="coeurs" nom="coeur" class="size-3.5 text-or-500" />
        </p>
        <component
            :is="balise"
            class="mt-3 text-[2rem] leading-[1.15] text-balance sm:text-[2.75rem]"
            :class="majuscules ? 'uppercase tracking-[0.14em]' : ''"
        >
            {{ titre }}
        </component>
        <p v-if="manuscrit" class="mt-3">
            <span class="manuscrit relative isolate inline-block px-7 pt-3 pb-1 text-[2.3rem] sm:text-[2.7rem]">
                <Pinceau class="text-poudre-200" />
                {{ manuscrit }}
            </span>
        </p>
        <slot />
    </div>
</template>
