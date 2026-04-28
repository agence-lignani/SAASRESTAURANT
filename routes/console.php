<?php

use App\Models\Restaurant;
use App\Services\Reservations\ExternalReservationSyncService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reservations:sync-external {--restaurant_id=}', function (ExternalReservationSyncService $syncService): void {
    $restaurantId = $this->option('restaurant_id');

    $restaurants = Restaurant::query()
        ->when(is_numeric($restaurantId), fn ($query) => $query->whereKey((int) $restaurantId))
        ->get();

    foreach ($restaurants as $restaurant) {
        $result = $syncService->syncRestaurant($restaurant);

        $this->line(sprintf(
            '[restaurant:%d] created=%d updated=%d skipped=%d',
            $restaurant->id,
            $result['created'],
            $result['updated'],
            $result['skipped'],
        ));
    }
})->purpose('Synchronise les réservations externes activées.');

Schedule::command('reservations:sync-external')->everyFiveMinutes()->withoutOverlapping();

Schedule::command('reservations:send-reminders')->everyFifteenMinutes()->withoutOverlapping();
