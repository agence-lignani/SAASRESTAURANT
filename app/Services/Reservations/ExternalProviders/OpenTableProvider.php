<?php

namespace App\Services\Reservations\ExternalProviders;

use App\Models\Restaurant;
use Carbon\CarbonImmutable;

class OpenTableProvider implements ExternalReservationProvider
{
    public function pull(Restaurant $restaurant, array $integrationConfig, CarbonImmutable $since): array
    {
        return [];
    }
}
