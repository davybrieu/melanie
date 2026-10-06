<script setup>
import { Link } from '@inertiajs/vue3';
import { inject } from 'vue';
import PageLegale from '../../components/PageLegale.vue';
import { useSite } from '../../composables/useSite';

defineProps({
    // Adresse e-mail publique, transmise à cette seule page (voir PageController).
    email: { type: String, required: true },
});

const route = inject('route');
const site = useSite();
</script>

<template>
    <PageLegale
        titre="Mentions légales"
        description="Mentions légales du site melanie-photographie.fr : éditeur, hébergeur, propriété intellectuelle des photographies de Mélanie Photographie."
        mise-a-jour="octobre 2026"
    >
        <h2>Éditeur du site</h2>
        <p>
            Le site <strong>melanie-photographie.fr</strong> est édité par <span class="a-completer">[À compléter : prénom et nom]</span>, entrepreneuse individuelle exerçant sous le nom commercial
            « {{ site.nom }} ».
        </p>
        <ul>
            <li>Statut : <span class="a-completer">[À compléter : statut, ex. micro-entreprise]</span></li>
            <li>Adresse : <span class="a-completer">[À compléter : adresse professionnelle]</span>, {{ site.code_postal }} {{ site.commune }}</li>
            <li>SIRET : <span class="a-completer">[À compléter : numéro SIRET]</span></li>
            <li>
                TVA : <span class="a-completer">[À confirmer : TVA non applicable, article 293 B du CGI]</span>
            </li>
            <li>E-mail : <a :href="`mailto:${email}`">{{ email }}</a></li>
            <li>
                Téléphone :
                <template v-if="site.telephone">{{ site.telephone }}</template>
                <span v-else class="a-completer">[À compléter : numéro de téléphone]</span>
            </li>
        </ul>
        <p>Directrice de la publication : <span class="a-completer">[À compléter : prénom et nom]</span>.</p>

        <h2>Hébergement</h2>
        <p>Le site est hébergé par <strong>Cloudflare, Inc.</strong></p>
        <ul>
            <li>Adresse : 101 Townsend St, San Francisco, CA 94107, États-Unis</li>
            <li>Téléphone : +1 650 319 8930</li>
        </ul>

        <h2>Propriété intellectuelle</h2>
        <p>
            L’ensemble des photographies, textes, logos et éléments graphiques présents sur ce site sont la propriété exclusive de {{ site.nom }}, sauf mention contraire, et
            sont protégés par le Code de la propriété intellectuelle. Toute reproduction, représentation, modification ou diffusion, totale ou partielle, sans autorisation
            écrite préalable est interdite.
        </p>

        <h2>Droit à l’image</h2>
        <p>
            Les photographies de personnes présentées sur ce site sont publiées avec l’accord écrit des personnes photographiées ou, pour les enfants, de leurs représentants
            légaux. Pour toute demande de retrait, écrivez à <a :href="`mailto:${email}`">{{ email }}</a>.
        </p>

        <h2>Données personnelles et cookies</h2>
        <p>
            Le traitement de vos données personnelles est détaillé dans la <Link :href="route('confidentialite')">politique de confidentialité</Link>. Ce site n’utilise
            que des cookies techniques, indispensables à son fonctionnement : aucun cookie publicitaire ni de mesure d’audience n’est déposé.
        </p>

        <h2>Crédits</h2>
        <p>Photographies : {{ site.nom }}. Polices : Gilda Display, Jost et Allison (licence SIL Open Font).</p>
    </PageLegale>
</template>
