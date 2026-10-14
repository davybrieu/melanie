<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BonCadeauRequest;
use App\Models\BonCadeau;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Bons cadeaux : liste, création, modification, utilisation et suppression. L'image à
 * imprimer est dessinée dans le navigateur, à partir de ces informations.
 */
class BonCadeauController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/BonsCadeaux', [
            'bons' => BonCadeau::latest('id')->get()->map(fn (BonCadeau $bon) => [
                'id' => $bon->id,
                'code' => $bon->code,
                'beneficiaire' => $bon->beneficiaire,
                'offert_par' => $bon->offert_par,
                'seance' => $bon->seance,
                'message' => $bon->message,
                'emis_le' => $bon->emis_le->toDateString(),
                'valable_jusqu_au' => $bon->valable_jusqu_au->toDateString(),
                'utilise_le' => $bon->utilise_le?->toDateString(),
                'statut' => $bon->statut(),
            ]),
            // Date du jour en France : date d'émission proposée pour un nouveau bon.
            'aujourdhui' => today('Europe/Paris')->toDateString(),
        ]);
    }

    public function store(BonCadeauRequest $request): RedirectResponse
    {
        $bon = BonCadeau::create($request->validated());

        Inertia::flash('bonCadeau', ['action' => 'cree', 'id' => $bon->id, 'code' => $bon->code]);

        return to_route('admin.bons-cadeaux');
    }

    public function update(BonCadeauRequest $request, BonCadeau $bonCadeau): RedirectResponse
    {
        $bonCadeau->update($request->validated());

        Inertia::flash('bonCadeau', ['action' => 'modifie', 'id' => $bonCadeau->id, 'code' => $bonCadeau->code]);

        return to_route('admin.bons-cadeaux');
    }

    /** Marque le bon comme utilisé, à la date du jour, ou annule son utilisation. */
    public function utilisation(Request $request, BonCadeau $bonCadeau): RedirectResponse
    {
        $bonCadeau->update(['utilise_le' => $request->boolean('utilise') ? today('Europe/Paris')->toDateString() : null]);

        return to_route('admin.bons-cadeaux');
    }

    public function destroy(BonCadeau $bonCadeau): RedirectResponse
    {
        $bonCadeau->delete();

        Inertia::flash('bonCadeau', ['action' => 'supprime', 'code' => $bonCadeau->code]);

        return to_route('admin.bons-cadeaux');
    }
}
