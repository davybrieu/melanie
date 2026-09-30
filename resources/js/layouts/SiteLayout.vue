<script setup>
import SiteFooter from '../components/SiteFooter.vue';
import SiteHeader from '../components/SiteHeader.vue';
</script>

<template>
    <a href="#contenu" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[60] focus:rounded-full focus:bg-cacao-800 focus:px-5 focus:py-3 focus:text-sm focus:text-creme-50">
        Aller au contenu
    </a>
    <SiteHeader />
    <main id="contenu">
        <slot />
    </main>
    <SiteFooter />
</template>
