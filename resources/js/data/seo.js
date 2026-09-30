// Données structurées schema.org (JSON-LD) réutilisées par plusieurs pages.

import { euros, seances } from './seances';

const prix = Object.values(seances).map((seance) => seance.prix);

export const faqJsonLd = (questions) => ({
    '@type': 'FAQPage',
    mainEntity: questions.map(({ q, r }) => ({
        '@type': 'Question',
        name: q,
        acceptedAnswer: { '@type': 'Answer', text: r },
    })),
});

export const entrepriseJsonLd = (site) => ({
    '@type': 'LocalBusiness',
    '@id': `${site.url}/#entreprise`,
    name: site.nom,
    description: `${site.metier} à ${site.ville} et alentours.`,
    url: `${site.url}/`,
    image: `${site.url}/images/marque/og-image.jpg`,
    logo: `${site.url}/images/marque/logo-mp-photographie.png`,
    email: site.email,
    ...(site.telephone ? { telephone: site.telephone } : {}),
    priceRange: `${euros(Math.min(...prix))} – ${euros(Math.max(...prix))}`,
    address: {
        '@type': 'PostalAddress',
        addressLocality: site.commune,
        postalCode: site.code_postal,
        addressRegion: site.region,
        addressCountry: 'FR',
    },
    areaServed: [site.ville, site.commune, site.departement].map((name) => ({ '@type': 'Place', name })),
    sameAs: [site.instagramUrl],
});

export const serviceJsonLd = (site, { nom, description, prix, url }) => ({
    '@type': 'Service',
    name: nom,
    serviceType: nom,
    description,
    url,
    provider: { '@id': `${site.url}/#entreprise` },
    areaServed: { '@type': 'City', name: site.ville },
    offers: { '@type': 'Offer', price: String(prix), priceCurrency: 'EUR' },
});
