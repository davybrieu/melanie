<?php

namespace App\Support;

/**
 * Le site est servi derrière Cloudflare : le serveur ne voit que l'adresse IP d'un serveur
 * Cloudflare, qui ajoute celle du visiteur dans l'en-tête X-Forwarded-For. bootstrap/app.php
 * ne lit cet en-tête que pour les requêtes venues de ces adresses : ailleurs, il est ignoré.
 *
 * Plages publiées par Cloudflare (https://www.cloudflare.com/ips/), relevées le 5 octobre 2026.
 * Elles changent très rarement ; si une plage manque, Laravel voit l'adresse de Cloudflare
 * au lieu de celle du visiteur, sans autre conséquence.
 */
class Cloudflare
{
    public const PLAGES_IP = [
        '173.245.48.0/20',
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '141.101.64.0/18',
        '108.162.192.0/18',
        '190.93.240.0/20',
        '188.114.96.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '162.158.0.0/15',
        '104.16.0.0/13',
        '104.24.0.0/14',
        '172.64.0.0/13',
        '131.0.72.0/22',
        '2400:cb00::/32',
        '2606:4700::/32',
        '2803:f800::/32',
        '2405:b500::/32',
        '2405:8100::/32',
        '2a06:98c0::/29',
        '2c0f:f248::/32',
    ];
}
