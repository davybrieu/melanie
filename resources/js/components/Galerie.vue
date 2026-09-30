<script setup>
import { computed, nextTick, onBeforeUnmount, ref } from 'vue';
import Icone from './Icone.vue';
import Photo from './Photo.vue';

const props = defineProps({
    photos: { type: Array, default: () => [] },
    // Emplacements affichés tant qu'aucune photo n'est déposée.
    vides: { type: Number, default: 6 },
    libelle: { type: String, default: 'Photo à venir' },
    colonnes: { type: String, default: 'columns-2 lg:columns-3' },
    sizes: { type: String, default: '(min-width: 1024px) 33vw, 50vw' },
});

// Alternance de formats pour que les emplacements vides ressemblent à une vraie galerie.
const formats = ['aspect-[4/5]', 'aspect-[3/4]', 'aspect-square', 'aspect-[2/3]', 'aspect-[5/4]', 'aspect-[4/5]'];

const index = ref(null);
const fermeture = ref(null);
let departGlisse = null;

const courante = computed(() => (index.value === null ? null : props.photos[index.value]));

function ouvrir(i) {
    index.value = i;
    document.documentElement.classList.add('overflow-hidden');
    window.addEventListener('keydown', clavier);
    nextTick(() => fermeture.value?.focus());
}

function fermer() {
    index.value = null;
    document.documentElement.classList.remove('overflow-hidden');
    window.removeEventListener('keydown', clavier);
}

function decaler(pas) {
    index.value = (index.value + pas + props.photos.length) % props.photos.length;
}

function clavier(evenement) {
    ({ Escape: fermer, ArrowLeft: () => decaler(-1), ArrowRight: () => decaler(1) })[evenement.key]?.();
}

function glisser(evenement) {
    if (departGlisse !== null && Math.abs(evenement.clientX - departGlisse) > 50) {
        decaler(evenement.clientX < departGlisse ? 1 : -1);
    }

    departGlisse = null;
}

onBeforeUnmount(() => index.value !== null && fermer());
</script>

<template>
    <div :class="['gap-3 sm:gap-4', colonnes]">
        <template v-if="photos.length">
            <button
                v-for="(photo, i) in photos"
                :key="photo.src"
                type="button"
                class="group mb-3 block w-full cursor-zoom-in break-inside-avoid overflow-hidden sm:mb-4"
                :aria-label="`Agrandir la photo : ${photo.alt}`"
                @click="ouvrir(i)"
            >
                <Photo :photo="photo" naturelle :sizes="sizes" class="transition-transform duration-700 group-hover:scale-[1.02]" />
            </button>
        </template>
        <template v-else>
            <Photo v-for="i in vides" :key="i" :libelle="libelle" :class="['mb-3 break-inside-avoid sm:mb-4', formats[(i - 1) % formats.length]]" />
        </template>
    </div>

    <template v-if="courante">
        <Teleport to="body">
            <div
                class="fixed inset-0 z-50 flex items-center justify-center bg-cacao-900/95 p-4 sm:p-10"
                role="dialog"
                aria-modal="true"
                :aria-label="courante.alt"
                @click.self="fermer"
                @pointerdown="departGlisse = $event.clientX"
                @pointerup="glisser"
            >
                <img
                    :key="courante.src"
                    :src="courante.src"
                    :srcset="courante.srcset"
                    sizes="92vw"
                    :alt="courante.alt"
                    class="max-h-[86vh] max-w-full object-contain shadow-2xl select-none"
                    draggable="false"
                />

                <button ref="fermeture" type="button" class="absolute top-4 right-4 grid size-11 place-items-center rounded-full text-creme-100 hover:bg-creme-50/10" aria-label="Fermer" @click="fermer">
                    <Icone nom="fermer" class="size-6" />
                </button>
                <template v-if="photos.length > 1">
                    <button type="button" class="absolute left-2 grid size-11 place-items-center rounded-full text-creme-100 hover:bg-creme-50/10 sm:left-5" aria-label="Photo précédente" @click="decaler(-1)">
                        <Icone nom="chevron-gauche" class="size-7" />
                    </button>
                    <button type="button" class="absolute right-2 grid size-11 place-items-center rounded-full text-creme-100 hover:bg-creme-50/10 sm:right-5" aria-label="Photo suivante" @click="decaler(1)">
                        <Icone nom="chevron-droit" class="size-7" />
                    </button>
                    <p class="absolute bottom-4 left-1/2 -translate-x-1/2 text-xs tracking-[0.3em] text-creme-200">{{ index + 1 }} / {{ photos.length }}</p>
                </template>
            </div>
        </Teleport>
    </template>
</template>
