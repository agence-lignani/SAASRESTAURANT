<?php

namespace App\Filament\Resources\BookingServices\Pages;

use App\Filament\Resources\BookingServices\BookingServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookingService extends CreateRecord
{
    protected static string $resource = BookingServiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['restaurant_id'] = app('filament.restaurant')->id;

        return $data;
    }
}
