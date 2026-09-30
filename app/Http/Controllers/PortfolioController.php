<?php

namespace App\Http\Controllers;

use App\Support\Photos;
use Inertia\Inertia;
use Inertia\Response;

class PortfolioController extends Controller
{
    public function __construct(private Photos $photos) {}

    public function index(): Response
    {
        return Inertia::render('Portfolio/Index', [
            'couvertures' => [
                'grossesse' => $this->photos->une('grossesse'),
                'nouveau-ne' => $this->photos->une('nouveau-ne'),
                'famille' => $this->photos->une('famille'),
            ],
        ]);
    }

    public function categorie(string $categorie): Response
    {
        return Inertia::render('Portfolio/Categorie', [
            'categorie' => $categorie,
            'photos' => $this->photos->dossier($categorie),
        ]);
    }
}
