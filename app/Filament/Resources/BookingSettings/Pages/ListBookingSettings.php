<?php

namespace App\Filament\Resources\BookingSettings\Pages;

use App\Filament\Resources\BookingSettings\BookingSettingResource;
use App\Models\BookingSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBookingSettings extends ListRecords
{
    protected static string $resource = BookingSettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => ! BookingSetting::query()->where('restaurant_id', app('filament.restaurant')->id)->exists()),
        ];
    }
}
