<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Une page n'a qu'une adresse. Laravel répond aussi, par défaut, à des variantes qui feraient
 * des pages en double :
 * - barre oblique finale (/tarifs/) : le routeur l'ignore (sur Apache, le .htaccess de Laravel
 *   les redirige, mais le site tourne sur nginx) ;
 * - point d'entrée dans l'adresse (/index.php, /index.php/tarifs) : nginx exécute index.php,
 *   et Laravel retire ce préfixe avant de chercher la page ;
 * - caractère encodé (/%74arifs pour /tarifs) : le routeur le décode. Aucune adresse du site
 *   n'en contient (adresses des photos comprises : leur nom passe par Str::slug).
 * Ces variantes reçoivent une 404, comme une adresse inconnue.
 */
class RefuserAdressesEnDouble
{
    public function handle(Request $request, Closure $next): Response
    {
        $chemin = $request->getPathInfo();

        abort_if(
            $request->getBaseUrl() !== ''
                || ($chemin !== '/' && str_ends_with($chemin, '/'))
                || str_contains($chemin, '%'),
            404,
        );

        return $next($request);
    }
}
