<?php

namespace App\Services\Reservations;

use App\Models\BookingService;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Services\Reservations\ExternalProviders\ExternalReservationProvider;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class ExternalReservationSyncService
{
    /**
     * @return array{created: int, updated: int, skipped: int}
     */
    public function syncRestaurant(Restaurant $restaurant): array
    {
        $result = ['created' => 0, 'updated' => 0, 'skipped' => 0];
        $settings = $restaurant->bookingSetting;

        if (! $settings) {
            return $result;
        }

        $since = now()
            ->subHours((int) config('reservations.sync_lookback_hours', 72))
            ->toImmutable();

        /** @var array<string, class-string<ExternalReservationProvider>> $providers */
        $providers = config('reservations.providers', []);

        foreach ($providers as $source => $providerClass) {
            $integration = $settings->integration($source);

            if (! $integration['enabled'] || blank($integration['api_key'])) {
                continue;
            }

            /** @var ExternalReservationProvider $provider */
            $provider = app($providerClass);

            foreach ($provider->pull($restaurant, $integration, $since) as $externalReservation) {
                $upserted = $this->upsertExternalReservation($restaurant, $source, $externalReservation);

                if ($upserted === 'created') {
                    $result['created']++;
                } elseif ($upserted === 'updated') {
                    $result['updated']++;
                } else {
                    $result['skipped']++;
                }
            }
        }

        return $result;
    }

    private function upsertExternalReservation(Restaurant $restaurant, string $source, array $externalReservation): string
    {
        $externalId = Arr::get($externalReservation, 'external_id');

        if (! is_string($externalId) || trim($externalId) === '') {
            return 'skipped';
        }

        $bookingServiceId = $this->resolveBookingServiceId($restaurant, $externalReservation);
        if (! $bookingServiceId) {
            Log::warning('External reservation skipped: booking service not matched', [
                'restaurant_id' => $restaurant->id,
                'source' => $source,
                'external_id' => $externalId,
            ]);

            return 'skipped';
        }

        $attributes = [
            'restaurant_id' => $restaurant->id,
            'source' => $source,
            'external_id' => $externalId,
        ];

        $values = [
            'booking_service_id' => $bookingServiceId,
            'reservation_at' => CarbonImmutable::parse((string) Arr::get($externalReservation, 'reservation_at')),
            'covers' => max(1, (int) Arr::get($externalReservation, 'covers', 1)),
            'customer_name' => (string) Arr::get($externalReservation, 'customer_name', 'Client externe'),
            'customer_email' => Arr::get($externalReservation, 'customer_email') ?: 'external@reservation.local',
            'customer_phone' => Arr::get($externalReservation, 'customer_phone'),
            'notes' => Arr::get($externalReservation, 'notes'),
            'status' => $this->normalizeStatus((string) Arr::get($externalReservation, 'status', Reservation::STATUS_PENDING)),
            'external_payload' => Arr::get($externalReservation, 'payload', $externalReservation),
            'synced_at' => now(),
        ];

        $existing = Reservation::query()->where($attributes)->first();
        Reservation::query()->updateOrCreate($attributes, $values);

        return $existing ? 'updated' : 'created';
    }

    private function resolveBookingServiceId(Restaurant $restaurant, array $externalReservation): ?int
    {
        $serviceId = Arr::get($externalReservation, 'booking_service_id');

        if (is_numeric($serviceId)) {
            $matched = $restaurant->bookingServices()->whereKey((int) $serviceId)->exists();

            return $matched ? (int) $serviceId : null;
        }

        $serviceName = Arr::get($externalReservation, 'booking_service_name');
        if (! is_string($serviceName) || trim($serviceName) === '') {
            return null;
        }

        /** @var BookingService|null $service */
        $service = $restaurant->bookingServices()->where('name', $serviceName)->first();

        return $service?->id;
    }

    private function normalizeStatus(string $status): string
    {
        return match (strtolower(trim($status))) {
            'confirmed', 'booked' => Reservation::STATUS_CONFIRMED,
            'cancelled', 'canceled' => Reservation::STATUS_CANCELLED,
            'refused', 'declined' => Reservation::STATUS_REFUSED,
            'no_show', 'noshow' => Reservation::STATUS_NO_SHOW,
            'delayed', 'late' => Reservation::STATUS_DELAYED,
            'attended', 'arrived', 'seated' => Reservation::STATUS_ATTENDED,
            default => Reservation::STATUS_PENDING,
        };
    }
}
