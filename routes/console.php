<?php

use Illuminate\Support\Facades\Schedule;

// Avis de la fiche Google, chaque matin (Scheduler de Forge à activer). sitemap.xml et
// robots.txt, eux, sont générés à chaque déploiement (php artisan seo:generer).
Schedule::command('avis:actualiser')->dailyAt('06:00')->timezone('Europe/Paris');
