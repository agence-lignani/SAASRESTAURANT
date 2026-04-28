<?php

namespace App\Mail;

use App\Models\ContactMessage;
use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * M9 — notification équipe suite formulaire contact (F11).
 */
class ContactMessageReceivedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactMessage $contactMessage,
        public Restaurant $restaurant,
    ) {}

    public function envelope(): Envelope
    {
        $subjectLine = '[Contact] '.($this->contactMessage->name).' — '.$this->restaurant->name;

        return new Envelope(
            subject: $subjectLine,
            replyTo: [
                new Address($this->contactMessage->email, $this->contactMessage->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.contact-message-received',
            with: [
                'message' => $this->contactMessage,
                'restaurant' => $this->restaurant,
            ],
        );
    }
}
