<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Admin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Connexion à l'espace de gestion, avec les identifiants du .env (App\Support\Admin).
 */
class ConnexionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Admin/Connexion');
    }

    public function store(Request $request): RedirectResponse
    {
        $identifiants = $request->validate(
            ['email' => ['required', 'string', 'max:255'], 'mot_de_passe' => ['required', 'string', 'max:255']],
            [],
            ['email' => 'adresse e-mail', 'mot_de_passe' => 'mot de passe'],
        );

        if (! Admin::verifier($identifiants['email'], $identifiants['mot_de_passe'])) {
            throw ValidationException::withMessages(['email' => 'Adresse e-mail ou mot de passe incorrect.']);
        }

        $request->session()->regenerate();
        $request->session()->put('admin', Admin::jeton());

        return redirect()->intended(route('admin.bons-cadeaux'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.connexion');
    }
}
