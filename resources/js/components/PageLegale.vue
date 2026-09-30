<script setup>
import FilAriane from './FilAriane.vue';
import Seo from './Seo.vue';
import TitreSection from './TitreSection.vue';

defineProps({
    titre: { type: String, required: true },
    description: { type: String, required: true },
    miseAJour: { type: String, required: true },
});
</script>

<template>
    <Seo :titre="titre" :description="description" />

    <section class="pt-8 pb-24 sm:pb-32">
        <div class="conteneur max-w-3xl">
            <FilAriane :liens="[{ libelle: titre }]" />
            <TitreSection class="mt-12" balise="h1" surtitre="Informations légales" :titre="titre" />
            <p class="mt-4 text-center text-sm text-taupe-500">Dernière mise à jour : {{ miseAJour }}</p>
            <div class="prose-mp mt-12 rounded-2xl border border-creme-300 bg-creme-50 p-6 sm:p-10">
                <slot />
            </div>
        </div>
    </section>
</template>
