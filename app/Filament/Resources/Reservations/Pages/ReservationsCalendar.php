<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\DB;

class ReservationsCalendar extends Page
{
    protected static string $resource = ReservationResource::class;

    protected string $view = 'filament.resources.reservations.pages.reservations-calendar';

    public string $monthStartDate = '';

    public string $selectedDate = '';

    public function mount(): void
    {
        $this->monthStartDate = now()->startOfMonth()->toDateString();
        $this->selectedDate = now()->toDateString();
    }

    public function getHeading(): string
    {
        return 'Calendrier des réservations';
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('list')
                ->label('Vue liste')
                ->icon('heroicon-o-list-bullet')
                ->url(ReservationResource::getUrl('index')),
            Action::make('new')
                ->label('Nouvelle réservation')
                ->icon('heroicon-o-plus')
                ->url(ReservationResource::getUrl('create'))
                ->visible(fn (): bool => auth()->user()?->can('create', Reservation::class) ?? false),
        ];
    }

    public function previousMonth(): void
    {
        $this->monthStartDate = CarbonImmutable::parse($this->monthStartDate)->subMonthNoOverflow()->startOfMonth()->toDateString();
    }

    public function nextMonth(): void
    {
        $this->monthStartDate = CarbonImmutable::parse($this->monthStartDate)->addMonthNoOverflow()->startOfMonth()->toDateString();
    }

    public function selectDate(string $date): void
    {
        $this->selectedDate = CarbonImmutable::parse($date)->toDateString();
    }

    public function getMonthLabel(): string
    {
        return CarbonImmutable::parse($this->monthStartDate)->locale('fr')->isoFormat('MMMM YYYY');
    }

    /**
     * @return array<int, array{date: string, day: int, inMonth: bool, total: int, confirmed: int, pending: int}>
     */
    public function getCalendarCells(): array
    {
        $restaurantId = app('filament.restaurant')->id;
        $monthStart = CarbonImmutable::parse($this->monthStartDate)->startOfMonth();
        $monthEnd = $monthStart->endOfMonth();
        $gridStart = $monthStart->startOfWeek(CarbonImmutable::MONDAY);
        $gridEnd = $monthEnd->endOfWeek(CarbonImmutable::SUNDAY);

        $rows = Reservation::query()
            ->where('restaurant_id', $restaurantId)
            ->whereBetween('reservation_at', [$gridStart->startOfDay(), $gridEnd->endOfDay()])
            ->selectRaw('DATE(reservation_at) as d')
            ->selectRaw('COUNT(*) as total')
            ->selectRaw("SUM(CASE WHEN status IN ('confirmed', 'delayed', 'attended') THEN 1 ELSE 0 END) as confirmed")
            ->selectRaw("SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending")
            ->groupBy(DB::raw('DATE(reservation_at)'))
            ->get()
            ->keyBy('d');

        $cells = [];
        for ($d = $gridStart; $d->lte($gridEnd); $d = $d->addDay()) {
            $key = $d->toDateString();
            $stat = $rows->get($key);

            $cells[] = [
                'date' => $key,
                'day' => $d->day,
                'inMonth' => $d->month === $monthStart->month,
                'total' => (int) ($stat?->total ?? 0),
                'confirmed' => (int) ($stat?->confirmed ?? 0),
                'pending' => (int) ($stat?->pending ?? 0),
            ];
        }

        return $cells;
    }

    /**
     * @return array<int, Reservation>
     */
    public function getSelectedDayReservations(): array
    {
        return Reservation::query()
            ->where('restaurant_id', app('filament.restaurant')->id)
            ->with('bookingService')
            ->whereDate('reservation_at', $this->selectedDate)
            ->orderBy('reservation_at')
            ->get()
            ->all();
    }
}
