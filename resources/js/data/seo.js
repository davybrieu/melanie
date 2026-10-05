// Données structurées schema.org (JSON-LD) réutilisées par plusieurs pages.
// N'y mettre que des informations publiées sur le site : ni adresse, ni horaires, ni avis inventés.

import { conditions } from './conditions';
import { euros, seances } from './seances';

const offres = Object.values(seances);
const prix = offres.map((seance) => seance.prix);

// Identifiants (@id) qui relient les données d'une page à l'autre.
const idSite = (site) => `${site.url}/#site`;
const idEntreprise = (site) => `${site.url}/#entreprise`;
const idSeance = (site, seance) => `${site.url}/#seance-${seance.cle}`;

// Adresse absolue construite sur APP_URL, comme l'URL canonique, jamais sur l'hôte de la requête.
const adresse = (site, route, nom, parametres) => site.url + route(nom, parametres, false);

const nomSeance = (seance) => `Séance photo ${seance.nom.toLowerCase()}`;

// Les réponses de la FAQ contiennent des liens relatifs (« /tarifs ») : on les rend absolus.
const liensAbsolus = (site, html) => html.replaceAll('href="/', `href="${site.url}/`);

// Accueil : nom du site affiché par Google dans les résultats.
export const siteWebJsonLd = (site) => ({
    '@type': 'WebSite',
    '@id': idSite(site),
    name: site.nom,
    url: `${site.url}/`,
    inLanguage: 'fr-FR',
    publisher: { '@id': idEntreprise(site) },
});

export const entrepriseJsonLd = (site) => ({
    '@type': 'LocalBusiness',
    '@id': idEntreprise(site),
    name: site.nom,
    description: `${site.metier} à ${site.ville} et alentours.`,
    url: `${site.url}/`,
    image: `${site.url}/images/marque/og-image.jpg`,
    logo: `${site.url}/images/marque/logo-mp.webp`,
    // Format international (+33…), comme le recommande Google.
    ...(site.telephoneUrl ? { telephone: site.telephoneUrl.replace('tel:', '') } : {}),
    priceRange: `${euros(Math.min(...prix))} – ${euros(Math.max(...prix))}`,
    currenciesAccepted: 'EUR',
    paymentAccepted: conditions.paiements,
    knowsLanguage: 'fr',
    // Ni rue ni coordonnées GPS : l'adresse professionnelle reste à fournir par Mélanie.
    address: {
        '@type': 'PostalAddress',
        addressLocality: site.commune,
        postalCode: site.code_postal,
        addressRegion: site.region,
        addressCountry: 'FR',
    },
    areaServed: [
        { '@type': 'City', name: site.ville },
        { '@type': 'City', name: site.commune },
        { '@type': 'AdministrativeArea', name: site.departement },
    ],
    hasOfferCatalog: {
        '@type': 'OfferCatalog',
        name: 'Séances photo',
        itemListElement: offres.map((seance) => ({
            '@type': 'Offer',
            price: String(seance.prix),
            priceCurrency: 'EUR',
            itemOffered: { '@type': 'Service', '@id': idSeance(site, seance), name: nomSeance(seance) },
        })),
    },
    sameAs: [site.instagramUrl],
});

// Une séance de data/seances.js, avec le même @id sur sa page et sur la page tarifs.
export const serviceJsonLd = (site, route, seance) => ({
    '@type': 'Service',
    '@id': idSeance(site, seance),
    name: nomSeance(seance),
    serviceType: `Photographie de ${seance.nom.toLowerCase()}`,
    description: `${nomSeance(seance)} à ${site.ville} et alentours : ${seance.inclus.join(', ').toLowerCase()}.`,
    url: adresse(site, route, seance.route),
    provider: { '@id': idEntreprise(site) },
    areaServed: { '@type': 'City', name: site.ville },
    termsOfService: adresse(site, route, 'cgv'),
    offers: {
        '@type': 'Offer',
        price: String(seance.prix),
        priceCurrency: 'EUR',
        url: adresse(site, route, 'contact', { seance: seance.cle }),
    },
});

export const bonCadeauJsonLd = (site, route, description) => ({
    '@type': 'Service',
    '@id': `${site.url}/#bon-cadeau`,
    name: 'Bon cadeau séance photo',
    serviceType: 'Bon cadeau',
    description,
    url: adresse(site, route, 'bon-cadeau'),
    provider: { '@id': idEntreprise(site) },
    areaServed: { '@type': 'City', name: site.ville },
    termsOfService: adresse(site, route, 'cgv'),
    offers: {
        '@type': 'AggregateOffer',
        lowPrice: String(Math.min(...prix)),
        highPrice: String(Math.max(...prix)),
        priceCurrency: 'EUR',
        offerCount: offres.length,
        url: adresse(site, route, 'contact', { objet: 'bon-cadeau' }),
    },
});

// Google demande de ne baliser chaque question qu'une fois sur tout le site :
// seule la page FAQ, qui les reprend toutes, utilise ce bloc.
export const faqJsonLd = (site, questions) => ({
    '@type': 'FAQPage',
    mainEntity: questions.map(({ q, r }) => ({
        '@type': 'Question',
        name: q,
        acceptedAnswer: { '@type': 'Answer', text: liensAbsolus(site, r) },
    })),
});

// Page typée (AboutPage, ContactPage, CollectionPage, ImageGallery…) rattachée au site.
export const pageJsonLd = (site, type, { nom, url, ...proprietes }) => ({
    '@type': type,
    name: nom,
    url,
    inLanguage: 'fr-FR',
    isPartOf: { '@id': idSite(site) },
    ...proprietes,
});

// Photo du portfolio : Google Images peut afficher le crédit et les droits d'auteur.
export const photoJsonLd = (site, photo) => ({
    '@type': 'ImageObject',
    contentUrl: site.url + photo.src,
    name: photo.alt,
    creator: { '@type': 'Organization', '@id': idEntreprise(site), name: site.nom },
    creditText: site.nom,
    copyrightNotice: `© ${site.nom}`,
});

export { adresse, idEntreprise, idSeance };
