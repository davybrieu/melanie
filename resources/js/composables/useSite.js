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

        return {
            ...site,
            instagramUrl: `https://www.instagram.com/${site.instagram}/`,
            telephoneUrl: numero ? `tel:+${numero}` : null,
            whatsappUrl: numero ? `https://wa.me/${numero}?text=${encodeURIComponent(messageWhatsapp)}` : null,
        };
    });
}
