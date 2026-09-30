<script setup>
import { Link } from '@inertiajs/vue3';
import { inject } from 'vue';
import PageLegale from '../../components/PageLegale.vue';
import { useSite } from '../../composables/useSite';
import { conditions as c } from '../../data/conditions';
import { euros, photoSupplementaire, photosIncluses, seances } from '../../data/seances';

const route = inject('route');
const site = useSite();
</script>

<template>
    <PageLegale
        titre="Conditions générales de vente"
        description="Conditions générales de vente des séances photo grossesse, nouveau-né et famille de Mélanie Photographie, à Dijon : réservation, paiement, report, livraison, droits."
        mise-a-jour="septembre 2026"
    >
        <h2>1. Objet</h2>
        <p>
            Les présentes conditions générales de vente (CGV) s’appliquent aux prestations de photographie proposées par {{ site.nom }},
            <span class="a-completer">[À compléter : prénom et nom, statut, SIRET, adresse]</span>, ci-après « la photographe », à tout client particulier. Toute réservation vaut
            acceptation des présentes CGV.
        </p>

        <h2>2. Prestations et tarifs</h2>
        <p>Les tarifs en vigueur sont indiqués sur la page <Link :href="route('tarifs')">Tarifs</Link> :</p>
        <ul>
            <li>séance grossesse ({{ seances.grossesse.duree }}) : {{ euros(seances.grossesse.prix) }} ;</li>
            <li>séance nouveau-né ({{ seances['nouveau-ne'].duree }}) : {{ euros(seances['nouveau-ne'].prix) }} ;</li>
            <li>séance famille ({{ seances.famille.duree }}) : {{ euros(seances.famille.prix) }} ;</li>
            <li>photo supplémentaire : {{ euros(photoSupplementaire) }}.</li>
        </ul>
        <p>
            Chaque séance comprend la prise de vue, le prêt de tenues, la retouche et la livraison de {{ photosIncluses }} photos en haute définition. Les prix sont
            indiqués en euros, <span class="a-completer">[À confirmer : TVA non applicable, article 293 B du CGI]</span>. Les déplacements sont inclus sur
            {{ c.zone }} ; au-delà, un supplément peut être proposé avant la réservation.
        </p>

        <h2>3. Réservation et acompte</h2>
        <p>
            La réservation devient ferme à réception d’un acompte de {{ c.acompte }} du montant de la prestation. Pour une séance nouveau-né, la réservation porte sur une
            période autour du terme ; la date exacte est fixée après la naissance.
        </p>

        <h2>4. Paiement</h2>
        <p>Le solde est réglé {{ c.solde }}, par {{ c.paiements }}. Les photos sont livrées après paiement intégral de la prestation.</p>

        <h2>5. Report et annulation</h2>
        <ul>
            <li>Report à la demande du client : possible sans frais s’il est demandé au moins {{ c.prevenance }} avant la séance.</li>
            <li>Maladie du client ou d’un enfant, naissance anticipée, météo défavorable pour une séance en extérieur : report sans frais.</li>
            <li>Annulation par le client : l’acompte reste acquis à la photographe.</li>
            <li>Annulation par la photographe (maladie, cas de force majeure) : report à une date convenue ensemble ou remboursement intégral des sommes versées.</li>
        </ul>

        <h2>6. Droit de rétractation</h2>
        <p>
            Lorsque la réservation est conclue à distance, le client dispose d’un délai de 14 jours pour exercer son droit de rétractation (article L221-18 du Code de la
            consommation), sauf si la séance a été réalisée avant la fin de ce délai à sa demande expresse.
            <span class="a-completer">[À faire valider par un professionnel du droit]</span>
        </p>

        <h2>7. Déroulement de la séance</h2>
        <p>
            Les durées de séance sont indicatives. Lors des séances nouveau-né, la sécurité et le bien-être de bébé priment : la photographe peut adapter ou interrompre une
            pose à tout moment, et un parent reste présent pendant toute la séance. Les tenues prêtées sont à rendre à la fin de la séance.
        </p>

        <h2>8. Livraison des photos</h2>
        <p>
            Après la séance, le client choisit ses photos dans une galerie de sélection. Les photos retouchées sont livrées {{ c.livraison }} après la sélection,
            {{ c.livraisonMode }}. Les fichiers bruts ne sont pas fournis. Il appartient au client de sauvegarder ses fichiers après téléchargement.
        </p>

        <h2>9. Propriété intellectuelle</h2>
        <p>
            Les photographies sont des œuvres protégées dont la photographe conserve les droits d’auteur. Le client bénéficie d’un droit d’utilisation privé et non
            commercial : impression, albums, partage sur ses réseaux personnels (en mentionnant @{{ site.instagram }} si possible). Toute utilisation commerciale, ainsi
            que la modification des photos (filtres, recadrages qui les dénaturent), nécessitent l’accord écrit de la photographe.
        </p>

        <h2>10. Droit à l’image</h2>
        <p>
            La photographe ne diffuse les photos d’une séance (site, réseaux sociaux, portfolio) qu’avec l’accord écrit du client et, pour les mineurs, des deux titulaires
            de l’autorité parentale. Cet accord peut être retiré à tout moment pour l’avenir.
        </p>

        <h2>11. Bons cadeaux</h2>
        <p>
            Les bons cadeaux sont valables {{ c.validiteBonCadeau }} à compter de leur émission. Ils ne sont ni remboursables, ni échangeables contre de l’argent. La séance
            est réservée par le bénéficiaire, dans les conditions des présentes CGV.
        </p>

        <h2>12. Responsabilité</h2>
        <p>
            La photographe est tenue à une obligation de moyens. En cas de perte ou de détérioration des fichiers due à un incident indépendant de sa volonté, sa
            responsabilité est limitée au remboursement des sommes versées.
        </p>

        <h2>13. Données personnelles</h2>
        <p>Voir la <Link :href="route('confidentialite')">politique de confidentialité</Link>.</p>

        <h2>14. Médiation de la consommation</h2>
        <p>
            En cas de litige, le client peut recourir gratuitement à un médiateur de la consommation (article L612-1 du Code de la consommation) :
            <span class="a-completer">[À compléter : nom et coordonnées du médiateur]</span>.
        </p>

        <h2>15. Droit applicable</h2>
        <p>Les présentes CGV sont soumises au droit français.</p>
    </PageLegale>
</template>
