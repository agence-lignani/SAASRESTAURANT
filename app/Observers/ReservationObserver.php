<?php

namespace App\Observers;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Mail\ReservationCancelledMail;
use App\Mail\ReservationConfirmedMail;
use App\Mail\ReservationRefusedMail;
use App\Models\Reservation;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Notifications\Notification as FilamentNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReservationObserver
{
    public function saving(Reservation $reservation): void
    {
        $reservation->markStatusTimestamps();
    }

    public function updated(Reservation $reservation): void
    {
        if (! $reservation->wasChanged('status')) {
            return;
        }

        $reservation->loadMissing('restaurant');

        $status = $reservation->status;
        $to = $reservation->customer_email;

        if ($status === Reservation::STATUS_CONFIRMED) {
            Mail::to($to)->queue(new ReservationConfirmedMail($reservation));
            $this->clearBackofficePendingNotifications($reservation);

            return;
        }

        if ($status === Reservation::STATUS_REFUSED) {
            Mail::to($to)->queue(new ReservationRefusedMail($reservation));
            $this->clearBackofficePendingNotifications($reservation);

            return;
        }

        if ($status === Reservation::STATUS_CANCELLED) {
            Mail::to($to)->queue(new ReservationCancelledMail($reservation));
            $this->clearBackofficePendingNotifications($reservation);

            return;
        }

        if (in_array($status, [Reservation::STATUS_DELAYED, Reservation::STATUS_ATTENDED], true)) {
            $this->clearBackofficePendingNotifications($reservation);

            return;
        }

        if ($status === Reservation::STATUS_PENDING) {
            $this->notifyBackoffice($reservation, 'updated');
        }
    }

    public function created(Reservation $reservation): void
    {
        $this->notifyBackoffice($reservation, 'created');
    }

    private function notifyBackoffice(Reservation $reservation, string $event): void
    {
        if ($reservation->status !== Reservation::STATUS_PENDING) {
            return;
        }

        $reservation->loadMissing('restaurant');
        $restaurantId = $reservation->restaurant_id;

        $users = User::query()
            ->whereHas('restaurants', function ($query) use ($restaurantId): void {
                $query
                    ->where('restaurants.id', $restaurantId)
                    ->whereIn('restaurant_user.role', ['owner', 'reservation', 'server']);
            })
            ->get();

        if ($users->isEmpty()) {
            return;
        }

        $detailUrl = ReservationResource::getUrl('view', ['record' => $reservation]);

        FilamentNotification::make()
            ->title($event === 'created' ? 'Nouvelle réservation' : 'Réservation mise à jour')
            ->safeViews('filament.notifications.pending-reservation')
            ->view('filament.notifications.pending-reservation')
            ->viewData([
                'customerName' => $reservation->customer_name,
                'dateLabel' => $reservation->reservation_at?->format('d/m/Y H:i') ?? '',
                'covers' => $reservation->covers,
                'status' => $reservation->status,
                'detailUrl' => $detailUrl,
                'reservationId' => $reservation->id,
            ])
            ->actions([
                Action::make('voir')
                    ->label('Voir le détail')
                    ->url($detailUrl)
                    ->markAsRead(),
            ])
            ->sendToDatabase($users);
    }

    private function clearBackofficePendingNotifications(Reservation $reservation): void
    {
        DB::table('notifications')
            ->where('type', 'Filament\\Notifications\\DatabaseNotification')
            ->whereRaw("json_extract(data, '$.view') = ?", ['filament.notifications.pending-reservation'])
            ->whereRaw("json_extract(data, '$.viewData.reservationId') = ?", [$reservation->id])
            ->delete();
    }
}
