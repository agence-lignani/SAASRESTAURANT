<?php

namespace App\Mail;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ReservationRefusedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        $fromAddress = $this->reservation->restaurant?->contact_email ?: config('mail.from.address');
        $fromName = $this->reservation->restaurant?->name ?: config('mail.from.name');

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: 'Votre réservation ne peut pas être acceptée'
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservations.refused',
            with: ['reservation' => $this->reservation],
        );
    }
}
