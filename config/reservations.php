<?php

use App\Services\Reservations\ExternalProviders\OpenTableProvider;
use App\Services\Reservations\ExternalProviders\TheForkProvider;
use App\Services\Reservations\ExternalProviders\ZenchefProvider;

return [
    'sync_lookback_hours' => 72,

    'providers' => [
        'thefork' => TheForkProvider::class,
        'opentable' => OpenTableProvider::class,
        'zenchef' => ZenchefProvider::class,
    ],
];
