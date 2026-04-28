<?php

namespace App\Services\Reservations\ExternalProviders;

use App\Models\Restaurant;
use Carbon\CarbonImmutable;

interface ExternalReservationProvider
{
    /**
     * @return array<int, array{
     *     external_id: string,
     *     reservation_at: string,
     *     covers: int,
     *     customer_name: string,
     *     customer_email: ?string,
     *     customer_phone: ?string,
     *     status: string,
     *     booking_service_id?: int,
     *     booking_service_name?: string,
     *     notes?: ?string,
     *     payload?: array<string, mixed>
     * }>
     */
    public function pull(Restaurant $restaurant, array $integrationConfig, CarbonImmutable $since): array;
}
