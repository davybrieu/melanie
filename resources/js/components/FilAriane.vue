<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { computed, inject } from 'vue';
import Icone from './Icone.vue';

// liens : [{ libelle, href? }] ; le dernier élément est la page courante.
const props = defineProps({
    liens: { type: Array, required: true },
});

const route = inject('route');

const elements = computed(() => [{ libelle: 'Accueil', href: route('accueil') }, ...props.liens]);

const jsonLd = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'BreadcrumbList',
        itemListElement: elements.value.map((lien, i) => ({
            '@type': 'ListItem',
            position: i + 1,
            name: lien.libelle,
            ...(lien.href ? { item: lien.href } : {}),
        })),
    }).replace(/</g, '\\u003c'),
);
</script>

<template>
    <nav aria-label="Fil d’Ariane" class="text-[0.7rem] tracking-[0.18em] text-taupe-500 uppercase">
        <ol class="flex flex-wrap items-center gap-x-2 gap-y-1">
            <li v-for="(lien, i) in elements" :key="i" class="inline-flex items-center gap-2">
                <Icone v-if="i > 0" nom="chevron-droit" class="size-3 text-or-400" />
                <Link v-if="lien.href && i < elements.length - 1" :href="lien.href" class="transition-colors hover:text-cacao-800">
                    {{ lien.libelle }}
                </Link>
                <span v-else aria-current="page" class="text-cacao-700">{{ lien.libelle }}</span>
            </li>
        </ol>
    </nav>
    <Head>
        <component :is="'script'" head-key="ld-fil-ariane" type="application/ld+json">{{ jsonLd }}</component>
    </Head>
</template>
