<?php

namespace App\Filament\Resources\BookingSettings\Pages;

use App\Filament\Resources\BookingSettings\BookingSettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBookingSetting extends CreateRecord
{
    protected static string $resource = BookingSettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['restaurant_id'] = app('filament.restaurant')->id;

        return $data;
    }
}
