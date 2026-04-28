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

class ReservationPendingClientMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Reservation $reservation) {}

    public function envelope(): Envelope
    {
        $fromAddress = $this->reservation->restaurant?->contact_email ?: config('mail.from.address');
        $restaurantName = $this->reservation->restaurant?->name ?: config('mail.from.name');
        $fromName = $restaurantName;

        return new Envelope(
            from: new Address($fromAddress, $fromName),
            subject: "{$restaurantName} - En attente de confirmation",
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.reservations.pending-client',
            with: ['reservation' => $this->reservation],
        );
    }
}
