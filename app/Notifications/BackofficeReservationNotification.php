<?php

namespace App\Notifications;

use App\Models\Reservation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class BackofficeReservationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        private readonly Reservation $reservation,
        private readonly string $event,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $dateLabel = $this->reservation->reservation_at?->format('d/m/Y H:i') ?? '';
        $status = $this->reservation->status;

        return [
            'title' => $this->event === 'created'
                ? 'Nouvelle réservation'
                : 'Réservation mise à jour',
            'message' => sprintf(
                '%s • %s • %d couverts • statut: %s',
                $this->reservation->customer_name,
                $dateLabel,
                $this->reservation->covers,
                $status
            ),
            'reservation_id' => $this->reservation->id,
            'event' => $this->event,
            'status' => $status,
            'url' => url('/admin/reservations'),
        ];
    }
}
