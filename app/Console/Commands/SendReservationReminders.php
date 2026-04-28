<?php

namespace App\Console\Commands;

use App\Mail\ReservationReminderMail;
use App\Models\Reservation;
use App\Models\Restaurant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendReservationReminders extends Command
{
    protected $signature = 'reservations:send-reminders';

    protected $description = 'Envoie les e-mails de rappel (M6) pour les réservations confirmées, selon les paramètres de chaque établissement.';

    public function handle(): int
    {
        foreach (Restaurant::query()->with('bookingSetting')->cursor() as $restaurant) {
            $settings = $restaurant->bookingSetting;
            if ($settings === null || ! $settings->reminder_enabled) {
                continue;
            }

            $hours = max(1, min(168, (int) $settings->reminder_hours_before));

            $candidates = Reservation::query()
                ->where('restaurant_id', $restaurant->id)
                ->where('status', Reservation::STATUS_CONFIRMED)
                ->whereNull('reminder_sent_at')
                ->where('reservation_at', '>', now())
                ->where('reservation_at', '<=', now()->addHours($hours))
                ->with(['restaurant.themeSetting', 'bookingService'])
                ->orderBy('id')
                ->get();

            foreach ($candidates as $reservation) {
                DB::transaction(function () use ($reservation, $hours): void {
                    $locked = Reservation::query()
                        ->whereKey($reservation->id)
                        ->where('restaurant_id', $reservation->restaurant_id)
                        ->where('status', Reservation::STATUS_CONFIRMED)
                        ->whereNull('reminder_sent_at')
                        ->where('reservation_at', '>', now())
                        ->where('reservation_at', '<=', now()->addHours($hours))
                        ->lockForUpdate()
                        ->first();

                    if ($locked === null) {
                        return;
                    }

                    $locked->forceFill(['reminder_sent_at' => now()])->saveQuietly();

                    Mail::to($locked->customer_email)->queue(new ReservationReminderMail($locked));
                });
            }
        }

        return self::SUCCESS;
    }
}
