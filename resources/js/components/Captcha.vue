<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';

// Case « Je suis un humain » d'hCaptcha (App\Support\Captcha vérifie le jeton côté serveur).
// Le script d'hCaptcha n'est chargé qu'à l'approche de la case, pour ne pas ralentir la page.
const props = defineProps({
    cle: { type: String, required: true },
    erreur: { type: String, default: null },
});

const jeton = defineModel({ type: String, default: '' });

const conteneur = ref(null);
let widget = null;
let observateur = null;

function chargerScript() {
    window.chargementHcaptcha ??= new Promise((resoudre, rejeter) => {
        window.surChargementHcaptcha = resoudre;
        const script = document.createElement('script');
        script.src = 'https://js.hcaptcha.com/1/api.js?render=explicit&hl=fr&onload=surChargementHcaptcha';
        script.async = true;
        script.onerror = rejeter;
        document.head.append(script);
    });

    return window.chargementHcaptcha;
}

async function afficher() {
    await chargerScript();

    if (!conteneur.value || widget !== null) return;

    widget = window.hcaptcha.render(conteneur.value, {
        sitekey: props.cle,
        // Version compacte quand la case standard (303 px) ne tient pas en largeur (petits téléphones)
        size: conteneur.value.parentElement.clientWidth < 303 ? 'compact' : 'normal',
        callback: (valeur) => (jeton.value = valeur),
        'expired-callback': () => (jeton.value = ''),
        'error-callback': () => (jeton.value = ''),
    });
}

// Après un refus du serveur, le jeton n'est plus valable : la case est à cocher de nouveau.
function reinitialiser() {
    if (widget !== null) window.hcaptcha.reset(widget);
    jeton.value = '';
}

defineExpose({ reinitialiser });

onMounted(() => {
    observateur = new IntersectionObserver(
        (entrees) => {
            if (entrees.some((entree) => entree.isIntersecting)) {
                observateur.disconnect();
                afficher();
            }
        },
        { rootMargin: '600px 0px' },
    );
    observateur.observe(conteneur.value);
});

onBeforeUnmount(() => {
    observateur?.disconnect();
    if (widget !== null) window.hcaptcha?.remove(widget);
});
</script>

<template>
    <div class="flex flex-col items-center">
        <!-- Hauteur réservée (78 px, celle de la case) : rien ne bouge quand elle apparaît -->
        <div
            ref="conteneur"
            class="min-h-[78px] rounded-[5px] transition-shadow"
            :class="erreur ? 'ring-2 ring-rouge ring-offset-2 ring-offset-creme-50' : ''"
        ></div>
        <p v-if="erreur" id="erreur-captcha" class="mt-2 text-center text-sm text-rouge">{{ erreur }}</p>
    </div>
</template>
