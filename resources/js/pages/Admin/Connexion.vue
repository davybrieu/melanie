<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { watch } from 'vue';
import Champ from '../../components/Champ.vue';
import AdminLayout from '../../layouts/AdminLayout.vue';

defineOptions({ layout: AdminLayout });

const page = usePage();

const form = useForm({
    email: '',
    mot_de_passe: '',
});

watch(
    () => ({ ...form.data() }),
    (actuel, precedent) => Object.keys(actuel).filter((cle) => actuel[cle] !== precedent[cle]).forEach((cle) => form.clearErrors(cle)),
);

// Adresse exclue de Ziggy (config/ziggy.php), comme toutes celles de l'espace de gestion.
function connecter() {
    form.post('/admin/connexion', {
        onFinish: () => form.reset('mot_de_passe'),
    });
}
</script>

<template>
    <Head title="Connexion | Mélanie Photographie" />

    <section class="conteneur max-w-md pt-16 sm:pt-24">
        <form class="rounded-2xl border border-creme-300 bg-creme-50 p-7 shadow-[0_18px_40px_-30px_rgba(75,52,40,0.35)] sm:p-10" novalidate @submit.prevent="connecter">
            <h1 class="text-center text-3xl">Espace bons cadeaux</h1>
            <p class="mt-3 text-center text-[0.95rem] text-cacao-700">Connectez-vous pour créer et suivre vos bons cadeaux.</p>

            <!-- Session expirée pendant une action (middleware EspaceAdmin) -->
            <p v-if="page.flash?.sessionExpiree" class="mt-6 rounded-xl bg-poudre-100 px-4 py-3 text-center text-[0.95rem] text-cacao-800" role="status">
                Votre session a expiré : reconnectez-vous, puis recommencez votre dernière action.
            </p>

            <div class="mt-8 grid gap-6">
                <Champ id="email" v-model="form.email" libelle="Adresse e-mail" :erreur="form.errors.email" type="email" autocomplete="username" />
                <Champ
                    id="mot_de_passe"
                    v-model="form.mot_de_passe"
                    libelle="Mot de passe"
                    :erreur="form.errors.mot_de_passe"
                    type="password"
                    autocomplete="current-password"
                />
            </div>

            <button
                type="submit"
                class="mt-9 inline-flex w-full cursor-pointer items-center justify-center rounded-full bg-cacao-800 px-8 py-4 text-[0.75rem] font-normal tracking-[0.24em] text-creme-50 uppercase transition-colors duration-300 hover:bg-brique disabled:opacity-60"
                :disabled="form.processing"
            >
                {{ form.processing ? 'Connexion…' : 'Se connecter' }}
            </button>
        </form>
    </section>
</template>
