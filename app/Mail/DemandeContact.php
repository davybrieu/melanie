<?php

namespace App\Mail;

use App\Http\Requests\DemandeContactRequest;
use App\Support\Telephone;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Carbon;

class DemandeContact extends Mailable
{
    /**
     * @param  array<string, mixed>  $demande
     */
    public function __construct(public array $demande) {}

    public function envelope(): Envelope
    {
        // Le numéro dans l'objet : visible dès la notification, pour rappeler sans attendre.
        return new Envelope(
            subject: "À rappeler : {$this->demande['prenom']} {$this->demande['nom']}, {$this->telephone()} ({$this->seances()})",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.demande-contact',
            with: [
                'seances' => $this->seances(),
                'telephone' => $this->telephone(),
                'lienTelephone' => Telephone::lien(Telephone::normaliser($this->demande['telephone'])),
                'dateAccouchement' => filled($this->demande['date_accouchement'] ?? null)
                    ? Carbon::parse($this->demande['date_accouchement'])->translatedFormat('j F Y')
                    : null,
            ],
        );
    }

    private function telephone(): string
    {
        return Telephone::affichage(Telephone::normaliser($this->demande['telephone']));
    }

    private function seances(): string
    {
        return collect($this->demande['seances'])
            ->map(fn (string $seance) => DemandeContactRequest::SEANCES[$seance])
            ->join(', ', ' et ');
    }
}
