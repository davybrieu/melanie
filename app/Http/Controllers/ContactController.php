<?php

namespace App\Http\Controllers;

use App\Http\Requests\DemandeContactRequest;
use App\Mail\DemandeContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends Controller
{
    public function create(Request $request): Response
    {
        return Inertia::render('Contact', [
            // /contact?seance=nouveau-ne ou ?objet=bon-cadeau pré-remplissent le formulaire.
            'seance' => array_key_exists($request->query('seance'), DemandeContactRequest::SEANCES) ? $request->query('seance') : null,
            'objet' => $request->query('objet') === 'bon-cadeau' ? 'bon-cadeau' : null,
        ]);
    }

    public function store(DemandeContactRequest $request): RedirectResponse
    {
        Mail::to(config('site.email'), config('site.nom'))->send(new DemandeContact($request->safe()->except('site_web')));

        Inertia::flash('demandeEnvoyee', true);

        return to_route('contact');
    }
}
