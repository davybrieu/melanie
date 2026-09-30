<script setup>
import FilAriane from './FilAriane.vue';
import Fleur from './Fleur.vue';
import Photo from './Photo.vue';
import Pinceau from './Pinceau.vue';

// En-tête des pages intérieures : fil d'Ariane, H1, phrase manuscrite, texte (slot) et grande photo.
defineProps({
    fil: { type: Array, required: true },
    surtitre: { type: String, default: null },
    titre: { type: String, required: true },
    manuscrit: { type: String, default: null },
    photo: { type: Object, default: null },
    libellePhoto: { type: String, default: 'Photo à venir' },
    arche: { type: Boolean, default: true },
});
</script>

<template>
    <section class="relative isolate overflow-hidden pt-8 pb-20 sm:pb-24">
        <Fleur variante="gypsophile" class="absolute top-0 -left-14 -z-10 w-40 opacity-70 sm:w-56" />
        <div class="conteneur">
            <FilAriane :liens="fil" />
            <div class="mt-10 grid items-center gap-12 lg:mt-14 lg:grid-cols-[minmax(0,1.05fr)_minmax(0,1fr)] lg:gap-16">
                <div class="text-center lg:text-left">
                    <p v-if="surtitre" class="surtitre">{{ surtitre }}</p>
                    <h1 class="mt-3 text-[2.35rem] leading-[1.1] text-balance sm:text-[3.2rem]">{{ titre }}</h1>
                    <p v-if="manuscrit" class="mt-4">
                        <span class="manuscrit relative isolate inline-block px-7 pt-3 pb-1 text-[2.2rem] sm:text-[2.6rem]">
                            <Pinceau class="text-poudre-200" />
                            {{ manuscrit }}
                        </span>
                    </p>
                    <slot />
                </div>
                <Photo
                    :photo="photo"
                    prioritaire
                    :libelle="libellePhoto"
                    sizes="(min-width: 1024px) 45vw, 100vw"
                    class="mx-auto aspect-[4/5] w-full max-w-md"
                    :class="arche ? 'rounded-t-full' : 'rounded-2xl'"
                />
            </div>
        </div>
    </section>
</template>
