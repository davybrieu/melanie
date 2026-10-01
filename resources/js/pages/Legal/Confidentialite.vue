<script setup>
import { Link } from '@inertiajs/vue3';
import { inject } from 'vue';
import PageLegale from '../../components/PageLegale.vue';
import { useSite } from '../../composables/useSite';

const route = inject('route');
const site = useSite();
</script>

<template>
    <PageLegale
        titre="Politique de confidentialité"
        description="Comment Mélanie Photographie, à Dijon, collecte, utilise et protège vos données personnelles, et comment exercer vos droits."
        mise-a-jour="octobre 2026"
    >
        <p>
            Vos photos et vos informations sont précieuses : je les traite avec le même soin que vos souvenirs. Cette page explique quelles données je collecte, pourquoi, et
            comment exercer vos droits, conformément au Règlement général sur la protection des données (RGPD). Mes coordonnées complètes figurent dans les
            <Link :href="route('mentions-legales')">mentions légales</Link>.
        </p>

        <h2>Données collectées</h2>
        <p>Via le formulaire de contact : prénom, nom, adresse e-mail, téléphone (facultatif), type de séance souhaitée, date prévue d’accouchement (facultative), nombre de personnes, période souhaitée et votre message.</p>
        <p>
            La date prévue d’accouchement révèle une grossesse : elle est facultative et, en la renseignant, vous acceptez qu’elle soit utilisée uniquement pour planifier votre
            séance (article 9.2.a du RGPD).
        </p>
        <p>Si vous m’écrivez sur Instagram, nos échanges sont également soumis à la politique de confidentialité d’Instagram.</p>

        <h2>Pourquoi ces données ?</h2>
        <ul>
            <li>répondre à votre demande et vous proposer une date ;</li>
            <li>préparer et réaliser votre séance, puis vous livrer vos photos ;</li>
            <li>établir les documents comptables (devis, factures).</li>
        </ul>
        <p>
            Ces traitements reposent sur les mesures précontractuelles prises à votre demande et sur l’exécution de la prestation (article 6.1.b du RGPD), ainsi que sur mes
            obligations légales pour la comptabilité (article 6.1.c).
        </p>

        <h2>Qui y a accès ?</h2>
        <p>
            Moi seule. Mes prestataires techniques (hébergement du site, messagerie) n’y accèdent que pour assurer leur service. Vos données ne sont jamais vendues ni cédées.
        </p>

        <h2>Combien de temps sont-elles conservées ?</h2>
        <ul>
            <li>demande restée sans suite : 3 ans après notre dernier échange ;</li>
            <li>clients : pendant la durée de la prestation, puis 3 ans ;</li>
            <li>factures : 10 ans (obligation légale) ;</li>
            <li>photos de la séance : aussi longtemps que les données clients, pour vous permettre de les récupérer en cas de perte.</li>
        </ul>

        <h2>Vos photos</h2>
        <p>
            Les photos de votre séance ne sont diffusées (site, réseaux sociaux) qu’avec votre accord écrit, et celui des deux parents pour les enfants mineurs. Vous pouvez
            retirer cet accord à tout moment.
        </p>

        <h2>Vos droits</h2>
        <p>
            Vous disposez d’un droit d’accès, de rectification, d’effacement, de limitation, d’opposition et de portabilité de vos données, ainsi que du droit de retirer votre
            consentement. Pour les exercer, écrivez-moi à <a :href="`mailto:${site.email}`">{{ site.email }}</a>. Si vous estimez que vos droits ne sont pas respectés, vous
            pouvez adresser une réclamation à la CNIL (<a href="https://www.cnil.fr" target="_blank" rel="noopener">cnil.fr</a>).
        </p>

        <h2>Cookies</h2>
        <p>
            Ce site dépose uniquement des cookies techniques indispensables (maintien de la session et sécurité du formulaire). Ils sont exemptés de consentement. Aucun outil de
            mesure d’audience ni cookie publicitaire n’est utilisé, et les polices d’écriture sont hébergées sur le site lui-même.
        </p>

        <h2>Sécurité</h2>
        <p>Le site est accessible en connexion chiffrée (HTTPS) et l’accès aux données est strictement limité.</p>
    </PageLegale>
</template>
