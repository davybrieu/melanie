<script setup>
import { inject } from 'vue';
import Bouton from './Bouton.vue';
import Faq from './Faq.vue';
import TitreSection from './TitreSection.vue';

defineProps({
    titre: { type: String, required: true },
    questions: { type: Array, required: true },
    ancre: { type: String, default: null },
});

const route = inject('route');
</script>

<template>
    <section id="faq" class="py-20 sm:py-28">
        <div class="conteneur max-w-3xl">
            <TitreSection surtitre="Questions fréquentes" :titre="titre" />
            <div class="mt-12">
                <Faq :questions="questions" />
            </div>
            <p class="mt-10 text-center">
                <Bouton :href="route('faq') + (ancre ? `#${ancre}` : '')" variante="lien">Toutes les questions</Bouton>
            </p>
        </div>
    </section>
</template>
