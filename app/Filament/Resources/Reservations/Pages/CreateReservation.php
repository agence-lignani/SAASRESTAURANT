<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Filament\Resources\Pages\CreateRecord;

class CreateReservation extends CreateRecord
{
    protected static string $resource = ReservationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['restaurant_id'] = app('filament.restaurant')->id;
        $data['cancel_token'] = bin2hex(random_bytes(24));
        $data['status'] = $data['status'] ?? Reservation::STATUS_PENDING;

        return $data;
    }
}
