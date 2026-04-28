<?php

namespace App\Mail;

use App\Models\Restaurant;
use App\Models\UserInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * M8 — invitation utilisateur back-office (lien signé).
 */
class UserInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public UserInvitation $invitation,
        public Restaurant $restaurant,
        public string $acceptUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Invitation — '.$this->restaurant->name.' ('.config('app.name').')',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.user-invitation',
            with: [
                'invitation' => $this->invitation,
                'restaurant' => $this->restaurant,
                'acceptUrl' => $this->acceptUrl,
            ],
        );
    }
}
