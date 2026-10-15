import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Numéro au format international, sans « + » (06 12 34 56 78 → 33612345678).
function international(telephone) {
    const chiffres = telephone.replace(/[^\d+]/g, '');

    if (chiffres.startsWith('+')) return chiffres.slice(1);
    if (chiffres.startsWith('00')) return chiffres.slice(2);
    if (chiffres.startsWith('0')) return `33${chiffres.slice(1)}`;

    return chiffres;
}

/** Informations de config/site.php, partagées par HandleInertiaRequests. */
export function useSite() {
    const page = usePage();

    return computed(() => {
        const site = page.props.site;
        const numero = site.telephone ? international(site.telephone) : null;
        const instagramUrl = `https://www.instagram.com/${site.instagram}/`;
        // Lien officiel de Google Maps vers la fiche (« Maps URLs »), par son Place ID.
        const googleUrl = site.google_place_id
            ? `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(site.nom)}&query_place_id=${encodeURIComponent(site.google_place_id)}`
            : null;

        return {
            ...site,
            instagramUrl,
            telephoneUrl: numero ? `tel:+${numero}` : null,
            googleUrl,
            // Google n'apparaît que si la fiche est renseignée (config/site.php).
            reseaux: [
                { nom: 'Instagram', url: instagramUrl, icone: 'instagram' },
                googleUrl && { nom: 'Google', url: googleUrl, icone: 'google' },
            ].filter(Boolean),
        };
    });
}
