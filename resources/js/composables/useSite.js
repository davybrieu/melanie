import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

// Message pré-rempli quand un visiteur ouvre WhatsApp depuis le site.
const messageWhatsapp = 'Bonjour Mélanie, je vous écris depuis votre site : j’aimerais en savoir plus sur vos séances photo.';

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
        const whatsappUrl = numero ? `https://wa.me/${numero}?text=${encodeURIComponent(messageWhatsapp)}` : null;
        const googleUrl = site.google_cid ? `https://www.google.com/maps?cid=${site.google_cid}` : null;

        return {
            ...site,
            instagramUrl,
            telephoneUrl: numero ? `tel:+${numero}` : null,
            whatsappUrl,
            googleUrl,
            // WhatsApp n'apparaît que si le téléphone est renseigné, Google que si la fiche l'est (config/site.php).
            reseaux: [
                { nom: 'Instagram', url: instagramUrl, icone: 'instagram' },
                whatsappUrl && { nom: 'WhatsApp', url: whatsappUrl, icone: 'whatsapp' },
                googleUrl && { nom: 'Google', url: googleUrl, icone: 'google' },
            ].filter(Boolean),
        };
    });
}
