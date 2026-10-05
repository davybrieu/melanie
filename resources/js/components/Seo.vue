<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    // Titre sans suffixe : « | Mélanie Photographie » est ajouté ci-dessous s'il tient.
    titre: { type: String, required: true },
    description: { type: String, required: true },
    jsonLd: { type: [Object, Array], default: null },
    indexer: { type: Boolean, default: true },
});

const page = usePage();
const site = computed(() => page.props.site);

// Google tronque les titres au-delà d'environ 60 caractères : le suffixe de marque n'est
// ajouté que s'il tient. Sinon, le titre reste seul (Google affiche de toute façon le nom
// du site au-dessus du résultat).
const titreComplet = computed(() => {
    const avecMarque = `${props.titre} | ${site.value.nom}`;

    return avecMarque.length <= 60 ? avecMarque : props.titre;
});

// Image de partage : JPEG, jamais WebP (LinkedIn et d'anciennes versions de WhatsApp ne l'affichent pas).
// Ses dimensions permettent aux réseaux d'afficher l'aperçu dès le premier partage.
const imagePartage = {
    chemin: '/images/marque/og-image.jpg',
    largeur: '1200',
    hauteur: '630',
    alt: 'Logo de Mélanie Photographie : monogramme MP dans un cercle doré, sur fond crème fleuri',
};
const image = computed(() => site.value.url + imagePartage.chemin);

// « < » échappé pour que le contenu ne puisse jamais fermer la balise <script>.
const scripts = computed(() =>
    [props.jsonLd]
        .flat()
        .filter(Boolean)
        .map((donnees) => JSON.stringify({ '@context': 'https://schema.org', ...donnees }).replace(/</g, '\\u003c')),
);
</script>

<template>
    <Head :title="titreComplet">
        <meta head-key="description" name="description" :content="description" />
        <meta v-if="!indexer" head-key="robots" name="robots" content="noindex, follow" />
        <link head-key="canonical" rel="canonical" :href="page.props.canonical" />
        <meta head-key="og:type" property="og:type" content="website" />
        <meta head-key="og:locale" property="og:locale" content="fr_FR" />
        <meta head-key="og:site_name" property="og:site_name" :content="site.nom" />
        <meta head-key="og:title" property="og:title" :content="titreComplet" />
        <meta head-key="og:description" property="og:description" :content="description" />
        <meta head-key="og:url" property="og:url" :content="page.props.canonical" />
        <meta head-key="og:image" property="og:image" :content="image" />
        <meta head-key="og:image:type" property="og:image:type" content="image/jpeg" />
        <meta head-key="og:image:width" property="og:image:width" :content="imagePartage.largeur" />
        <meta head-key="og:image:height" property="og:image:height" :content="imagePartage.hauteur" />
        <meta head-key="og:image:alt" property="og:image:alt" :content="imagePartage.alt" />
        <meta head-key="twitter:card" name="twitter:card" content="summary_large_image" />
        <meta head-key="twitter:image:alt" name="twitter:image:alt" :content="imagePartage.alt" />
        <component :is="'script'" v-for="(json, i) in scripts" :key="i" :head-key="`ld-${i}`" type="application/ld+json">{{ json }}</component>
    </Head>
</template>
