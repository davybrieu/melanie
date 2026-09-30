<?php

namespace App\Mail;

use App\Http\Requests\DemandeContactRequest;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
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
        return new Envelope(
            replyTo: [new Address($this->demande['email'], "{$this->demande['prenom']} {$this->demande['nom']}")],
            subject: "Nouvelle demande de {$this->demande['prenom']} {$this->demande['nom']} ({$this->seances()})",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.demande-contact',
            with: [
                'seances' => $this->seances(),
                'dateAccouchement' => filled($this->demande['date_accouchement'] ?? null)
                    ? Carbon::parse($this->demande['date_accouchement'])->translatedFormat('j F Y')
                    : null,
            ],
        );
    }

    private function seances(): string
    {
        return collect($this->demande['seances'])
            ->map(fn (string $seance) => DemandeContactRequest::SEANCES[$seance])
            ->join(', ', ' et ');
    }
}
