<?php

use Illuminate\Support\Facades\Schedule;

// Tâches planifiées, lancées par `php artisan schedule:run` chaque minute : sur Forge, le
// réglage « Laravel Scheduler » du site crée cette tâche cron. sitemap.xml et robots.txt,
// eux, sont générés à chaque déploiement (php artisan seo:generer).

// Note et avis de la fiche Google, chaque matin.
Schedule::command('avis:actualiser')->dailyAt('06:00')->timezone('Europe/Paris');
