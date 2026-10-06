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
        <p>Via le formulaire de contact : prénom, nom, numéro de téléphone, type de séance souhaitée, nombre de personnes, période souhaitée et votre message.</p>
        <p>Si vous m’écrivez sur Instagram, nos échanges sont également soumis à la politique de confidentialité d’Instagram.</p>

        <h2>Pourquoi ces données ?</h2>
        <ul>
            <li>vous rappeler pour répondre à votre demande et vous proposer une date ;</li>
            <li>préparer et réaliser votre séance, puis vous livrer vos photos ;</li>
            <li>établir les documents comptables (devis, factures).</li>
        </ul>
        <p>
            Ces traitements reposent sur les mesures précontractuelles prises à votre demande et sur l’exécution de la prestation (article 6.1.b du RGPD), ainsi que sur mes
            obligations légales pour la comptabilité (article 6.1.c).
        </p>

        <h2>Protection contre les robots</h2>
        <p>
            Le formulaire de contact est protégé par hCaptcha, un service d’Intuition Machines, Inc., qui vérifie qu’une demande est envoyée par une personne et non par un
            robot. Il n’est chargé qu’à l’approche du bouton d’envoi et reçoit pour cela des informations techniques (adresse IP, navigateur, interaction avec la case
            « Je suis un humain »). Ce contrôle repose sur mon intérêt légitime à protéger le formulaire contre les envois automatisés (article 6.1.f du RGPD). Pour en savoir plus,
            consultez la <a href="https://www.hcaptcha.com/privacy" target="_blank" rel="noopener">politique de confidentialité d’hCaptcha</a>.
        </p>

        <h2>Données de connexion</h2>
        <p>
            Comme sur tout site, votre adresse IP est transmise au serveur à chaque visite. Elle sert à limiter les envois répétés du formulaire et n’est conservée qu’avec votre
            session de navigation, qui expire après deux heures d’inactivité, puis est effacée. Ce traitement repose sur mon intérêt légitime à sécuriser le site (article 6.1.f du
            RGPD).
        </p>

        <h2>Mesure d’audience</h2>
        <p>
            Pour savoir quelles pages vous intéressent et vérifier que le site s’affiche vite, j’utilise Cloudflare Web Analytics, un service de Cloudflare, Inc., qui protège
            et accélère déjà le site. À chaque page vue, il mesure le temps de chargement et relève la page consultée, le site d’où vous venez, le type d’appareil, le navigateur,
            le système et le pays. Il ne dépose aucun cookie, n’enregistre rien sur votre appareil et ne vous suit pas d’un site à l’autre : je n’en vois que des statistiques
            globales, qui ne permettent pas de vous identifier. Cette mesure repose sur mon intérêt légitime à améliorer le site (article 6.1.f du RGPD). Cloudflare, Inc. est une
            société américaine : le transfert de données vers les États-Unis est encadré par sa certification au Data Privacy Framework UE–États-Unis. Pour en savoir plus,
            consultez la <a href="https://www.cloudflare.com/privacypolicy/" target="_blank" rel="noopener">politique de confidentialité de Cloudflare</a>.
        </p>

        <h2>Qui y a accès ?</h2>
        <p>
            Moi seule. Mes prestataires techniques (hébergement et protection du site, messagerie, protection du formulaire, mesure d’audience) n’y accèdent que pour assurer leur service. Vos données ne sont
            jamais vendues ni cédées.
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
            Ce site dépose uniquement des cookies techniques indispensables (maintien de la session, sécurité du site et du formulaire, y compris ceux d’hCaptcha sur la page
            de contact et, si besoin, de Cloudflare). Ils sont exemptés de consentement. La mesure d’audience n’en dépose aucun, aucun cookie publicitaire n’est utilisé, et les
            polices d’écriture sont hébergées sur le site lui-même.
        </p>

        <h2>Sécurité</h2>
        <p>Le site est accessible en connexion chiffrée (HTTPS) et l’accès aux données est strictement limité.</p>
    </PageLegale>
</template>
