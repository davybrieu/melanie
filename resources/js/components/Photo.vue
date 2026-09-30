<script setup>
import Icone from './Icone.vue';

// Affiche une photo de resources/photos, ou un emplacement aux couleurs du site si elle manque.
// La hauteur vient d'une classe d'aspect (aspect-[4/5]…) passée par le parent, sauf en mode « naturelle ».
defineProps({
    photo: { type: Object, default: null },
    alt: { type: String, default: null },
    sizes: { type: String, default: '100vw' },
    libelle: { type: String, default: 'Photo à venir' },
    prioritaire: { type: Boolean, default: false },
    naturelle: { type: Boolean, default: false },
});
</script>

<template>
    <div class="relative overflow-hidden bg-creme-200">
        <img
            v-if="photo"
            :src="photo.src"
            :srcset="photo.srcset"
            :sizes="sizes"
            :width="photo.largeur"
            :height="photo.hauteur"
            :alt="alt ?? photo.alt"
            :loading="prioritaire ? 'eager' : 'lazy'"
            :fetchpriority="prioritaire ? 'high' : 'auto'"
            decoding="async"
            :class="naturelle ? 'block h-auto w-full' : 'absolute inset-0 size-full object-cover'"
        />
        <div
            v-else
            role="img"
            :aria-label="alt ?? libelle"
            class="absolute inset-0 flex flex-col items-center justify-center gap-3 rounded-[inherit] bg-linear-to-br from-creme-200 via-poudre-100 to-creme-300 p-6 text-center"
        >
            <span class="absolute inset-3 rounded-[inherit] border border-or-300/70" aria-hidden="true"></span>
            <Icone nom="appareil" class="size-8 text-or-500" />
            <span class="surtitre text-[0.62rem] leading-relaxed">{{ libelle }}</span>
        </div>
    </div>
</template>
