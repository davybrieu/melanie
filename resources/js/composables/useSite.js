import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

/** Informations de config/site.php, partagées par HandleInertiaRequests. */
export function useSite() {
    const page = usePage();

    return computed(() => ({
        ...page.props.site,
        instagramUrl: `https://www.instagram.com/${page.props.site.instagram}/`,
    }));
}
