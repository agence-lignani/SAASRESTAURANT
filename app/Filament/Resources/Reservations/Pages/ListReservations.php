<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use App\Support\Filament\FilamentAccess;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Size;
use Illuminate\Database\Eloquent\Builder;

class ListReservations extends ListRecords
{
    protected static string $resource = ReservationResource::class;

    public function mount(): void
    {
        if (FilamentAccess::isServer() && $this->tableFilters === null) {
            $this->tableFilters = [
                'reservation_day' => [
                    'day' => now()->toDateString(),
                ],
            ];
        }

        parent::mount();
    }

    protected function getTableQuery(): Builder
    {
        return parent::getTableQuery()->with('bookingService');
    }

    protected function getHeaderActions(): array
    {
        $calendar = Action::make('calendar')
            ->label('Calendrier')
            ->icon('heroicon-o-calendar-days')
            ->url(ReservationResource::getUrl('calendar'));

        if (FilamentAccess::isServer()) {
            $calendar->size(Size::Large);
        }

        return [
            $calendar,
            CreateAction::make()
                ->visible(fn (): bool => auth()->user()?->can('create', Reservation::class) ?? false),
        ];
    }
}
