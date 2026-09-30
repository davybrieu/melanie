<template>
    <!-- Coup de pinceau poudré derrière les phrases manuscrites (flyer). Le parent doit être « relative isolate ». -->
    <svg
        class="pointer-events-none absolute inset-0 -z-10 size-full"
        viewBox="0 0 400 60"
        preserveAspectRatio="none"
        aria-hidden="true"
        focusable="false"
    >
        <path
            fill="currentColor"
            d="M6 31C17 15 55 16 104 14c69-3 150 1 222-3 32-2 58 2 67 10 5 8-1 18-13 22-49 8-131 4-212 6-60 2-118 4-150-2C8 45 1 38 6 31z"
        />
        <path
            fill="none"
            stroke="currentColor"
            stroke-width="2.5"
            stroke-linecap="round"
            opacity="0.55"
            d="M24 19c86-7 198-9 348-4M16 44c104 5 222 3 364-4M40 51c90 2 170 1 260-2"
        />
    </svg>
</template>
