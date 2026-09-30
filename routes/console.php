<?php

use Illuminate\Support\Facades\Schedule;

// sitemap.xml, robots.txt et llms.txt : régénérés chaque nuit (<lastmod> = date du dernier changement réel).
Schedule::command('seo:generer')
    ->dailyAt('04:00')
    ->timezone(config('seo.fuseau_horaire'))
    ->withoutOverlapping();
