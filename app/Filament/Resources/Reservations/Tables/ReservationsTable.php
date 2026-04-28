<?php

namespace App\Filament\Resources\Reservations\Tables;

use App\Models\Reservation;
use App\Support\Filament\FilamentAccess;
use Carbon\CarbonImmutable;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\Indicator;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ReservationsTable
{
    public static function configure(Table $table): Table
    {
        $isServer = FilamentAccess::isServer();

        $table = $table
            ->columns($isServer ? self::serverColumns() : self::managerColumns())
            ->filters([
                Filter::make('reservation_day')
                    ->label('Date')
                    ->schema([
                        DatePicker::make('day')
                            ->label('Jour')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $bounds = self::reservationDayBounds($data['day'] ?? null);
                        if ($bounds === null) {
                            return;
                        }

                        [$start, $end] = $bounds;
                        $query->whereBetween('reservation_at', [$start, $end]);
                    })
                    ->indicateUsing(function (array $state): array {
                        $bounds = self::reservationDayBounds($state['day'] ?? null);
                        if ($bounds === null) {
                            return [];
                        }

                        return [
                            Indicator::make('Date : '.$bounds[0]->format('d/m/Y')),
                        ];
                    }),
                Filter::make('customer_family_name')
                    ->label('Nom de famille')
                    ->schema([
                        TextInput::make('value')
                            ->label('Nom de famille')
                            ->placeholder('ex. Dupont')
                            ->maxLength(120),
                    ])
                    ->query(function (Builder $query, array $data): void {
                        $term = trim((string) ($data['value'] ?? ''));
                        if ($term === '') {
                            return;
                        }

                        $needle = mb_strtolower($term);
                        $needle = addcslashes($needle, '%_\\');

                        $query->where(function (Builder $q) use ($needle): void {
                            $q->whereRaw('LOWER(TRIM(customer_name)) = ?', [$needle])
                                ->orWhereRaw('LOWER(TRIM(customer_name)) LIKE ?', ['% '.$needle.'%']);
                        });
                    })
                    ->indicateUsing(function (array $state): array {
                        if (blank($state['value'] ?? null)) {
                            return [];
                        }

                        return [
                            Indicator::make('Nom : '.(string) $state['value']),
                        ];
                    }),
                SelectFilter::make('status')
                    ->options([
                        Reservation::STATUS_PENDING => 'En attente',
                        Reservation::STATUS_CONFIRMED => 'Confirmée',
                        Reservation::STATUS_DELAYED => 'Retard',
                        Reservation::STATUS_ATTENDED => 'Présence confirmée',
                        Reservation::STATUS_REFUSED => 'Refusée',
                        Reservation::STATUS_CANCELLED => 'Annulée',
                        Reservation::STATUS_NO_SHOW => 'No-show',
                    ]),
            ])
            ->defaultSort(
                'reservation_at',
                $isServer ? 'asc' : 'desc',
            )
            ->recordActions([
                ViewAction::make()
                    ->when($isServer, fn (ViewAction $action) => $action
                        ->label('Fiche')
                        ->size(Size::Large)),
                EditAction::make()
                    ->visible(fn (Reservation $record): bool => auth()->user()?->can('update', $record) ?? false),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ])->visible(fn (): bool => FilamentAccess::canManageBookings()),
            ]);

        if ($isServer) {
            $table
                ->filtersLayout(FiltersLayout::AboveContentCollapsible)
                ->filtersFormColumns(1)
                ->paginationPageOptions([10, 15, 25, 50])
                ->recordActionsAlignment('end');
        }

        return $table;
    }

    /**
     * @return array<Stack|TextColumn>
     */
    private static function serverColumns(): array
    {
        return [
            Stack::make([
                TextColumn::make('reservation_at')
                    ->label('Créneau')
                    ->dateTime('d/m · H:i')
                    ->sortable()
                    ->weight(FontWeight::SemiBold),
                TextColumn::make('customer_name')
                    ->label('Client')
                    ->searchable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('bookingService.name')
                    ->label('')
                    ->formatStateUsing(function (?string $state, Reservation $record): string {
                        $service = $state ?? '—';

                        return $service.' · '.$record->covers.' pers.';
                    })
                    ->color('gray')
                    ->icon('heroicon-o-building-storefront'),
                self::statusBadgeColumn()
                    ->label(''),
            ])->space(2),
        ];
    }

    /**
     * @return array<TextColumn>
     */
    private static function managerColumns(): array
    {
        return [
            TextColumn::make('reservation_at')->label('Date')->dateTime('d/m/Y H:i')->sortable(),
            TextColumn::make('bookingService.name')->label('Service')->sortable(),
            TextColumn::make('customer_name')->label('Client')->searchable(),
            TextColumn::make('covers')->label('Couverts')->sortable(),
            self::statusBadgeColumn(),
        ];
    }

    private static function statusBadgeColumn(): TextColumn
    {
        return TextColumn::make('status')
            ->label('Statut')
            ->badge()
            ->formatStateUsing(fn (string $state): string => match ($state) {
                Reservation::STATUS_PENDING => 'En attente',
                Reservation::STATUS_CONFIRMED => 'Confirmée',
                Reservation::STATUS_DELAYED => 'Retard',
                Reservation::STATUS_ATTENDED => 'Présente',
                Reservation::STATUS_REFUSED => 'Refusée',
                Reservation::STATUS_CANCELLED => 'Annulée',
                Reservation::STATUS_NO_SHOW => 'No-show',
                default => $state,
            })
            ->color(fn (string $state): string => match ($state) {
                Reservation::STATUS_CONFIRMED, Reservation::STATUS_ATTENDED => 'success',
                Reservation::STATUS_REFUSED => 'danger',
                Reservation::STATUS_CANCELLED, Reservation::STATUS_NO_SHOW => 'gray',
                Reservation::STATUS_DELAYED => 'warning',
                default => 'warning',
            });
    }

    /**
     * @return array{0: CarbonImmutable, 1: CarbonImmutable}|null
     */
    private static function reservationDayBounds(mixed $day): ?array
    {
        if ($day === null || $day === '') {
            return null;
        }

        $tz = config('app.timezone');

        try {
            if ($day instanceof \DateTimeInterface) {
                $start = CarbonImmutable::parse($day)->timezone($tz)->startOfDay();
            } else {
                $str = trim((string) $day);
                if ($str === '') {
                    return null;
                }

                $start = CarbonImmutable::parse($str, $tz)->startOfDay();
            }
        } catch (\Throwable) {
            return null;
        }

        return [$start, $start->endOfDay()];
    }
}
