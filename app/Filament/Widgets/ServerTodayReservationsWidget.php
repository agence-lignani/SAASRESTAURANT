<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use App\Support\Filament\FilamentAccess;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ServerTodayReservationsWidget extends StatsOverviewWidget
{
    protected static ?int $sort = -2;

    public static function canView(): bool
    {
        return FilamentAccess::isServer();
    }

    protected ?string $heading = 'Réservations du jour';

    protected function getDescription(): ?string
    {
        return CarbonImmutable::now(config('app.timezone'))
            ->locale('fr')
            ->isoFormat('dddd D MMMM YYYY');
    }

    /**
     * @return array<int, Stat>
     */
    protected function getStats(): array
    {
        $restaurantId = app('filament.restaurant')->id;
        $tz = config('app.timezone');
        $start = CarbonImmutable::now($tz)->startOfDay();
        $end = CarbonImmutable::now($tz)->endOfDay();

        $base = Reservation::query()
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('reservation_at', [$start, $end]);

        $total = (clone $base)->count();
        $pending = (clone $base)->where('status', Reservation::STATUS_PENDING)->count();
        $active = (clone $base)->whereIn('status', [
            Reservation::STATUS_CONFIRMED,
            Reservation::STATUS_DELAYED,
        ])->count();

        $listUrl = ReservationResource::getUrl('index');

        return [
            Stat::make('Total', $total)
                ->description('Sur la journée')
                ->icon('heroicon-o-calendar-days')
                ->color('gray')
                ->url($listUrl),
            Stat::make('En attente', $pending)
                ->description('À confirmer côté salle')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->url($listUrl),
            Stat::make('Confirmées / retard', $active)
                ->description('Créneaux actifs')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->url($listUrl),
        ];
    }
}
