# Melanie

Laravel 13 · Vue 3 · Inertia v3 (SSR) · Ziggy · Tailwind CSS 4 · interface en français.

## Prérequis

- PHP 8.3+ (avec l'extension `bcmath`, requise par Laravel Lang en développement)
- Composer
- Node.js 20.19+ ou 22.12+

## Installation

```bash
composer setup
```

Installe les dépendances, crée `.env`, génère la clé, lance les migrations (SQLite par défaut) et compile les assets.

## Développement

```bash
composer dev
```

Lance le serveur Laravel, la file d'attente et Vite. En développement, le SSR est assuré directement par le serveur Vite (plugin `@inertiajs/vite`) : aucun processus Node séparé n'est nécessaire.

## Production

```bash
npm run build                 # bundle client (public/build) + bundle SSR (bootstrap/ssr)
php artisan inertia:start-ssr # serveur SSR Node, à garder actif (Supervisor, etc.)
```

Sans serveur SSR actif, Inertia revient automatiquement au rendu côté client. Le SSR peut être désactivé avec `INERTIA_SSR_ENABLED=false`.

## Structure

L'application est vide : aucune route, aucune page.

- `routes/web.php` : routes (ex. `Route::inertia('/', 'Accueil')->name('accueil')`)
- `resources/js/pages/` : pages Inertia (ex. `Accueil.vue`)
- `resources/js/app.js` : point d'entrée client **et** SSR
- `resources/views/app.blade.php` : template racine Inertia

## Routes côté Vue (Ziggy)

Dans les templates, `route()` est disponible directement :

```vue
<Link :href="route('accueil')">Accueil</Link>
```

Dans `<script setup>`, passer par `inject` pour que le code fonctionne aussi en SSR :

```vue
<script setup>
import { inject } from 'vue';

const route = inject('route');
</script>
```

## Traductions

Les fichiers français sont dans `lang/fr` et `lang/fr.json` (générés par [Laravel Lang](https://laravel-lang.com)). Pour les mettre à jour :

```bash
php artisan lang:update
```
