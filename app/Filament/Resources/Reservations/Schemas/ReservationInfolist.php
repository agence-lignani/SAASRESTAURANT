<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Models\Reservation;
use App\Support\Filament\FilamentAccess;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReservationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Réservation')
                    ->schema([
                        TextEntry::make('bookingService.name')->label('Service'),
                        TextEntry::make('reservation_at')->label('Date et heure')->dateTime('d/m/Y H:i'),
                        TextEntry::make('covers')->label('Couverts'),
                        TextEntry::make('status')
                            ->label('Statut')
                            ->badge()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                Reservation::STATUS_PENDING => 'En attente',
                                Reservation::STATUS_CONFIRMED => 'Confirmée',
                                Reservation::STATUS_DELAYED => 'Retard',
                                Reservation::STATUS_ATTENDED => 'Présence confirmée',
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
                            }),
                        TextEntry::make('customer_name')->label('Nom client'),
                        TextEntry::make('customer_email')
                            ->label('E-mail')
                            ->copyable()
                            ->visible(fn (): bool => ! FilamentAccess::isServer()),
                        TextEntry::make('customer_phone')->label('Téléphone')->placeholder('—'),
                        TextEntry::make('notes')->label('Notes')->columnSpanFull()->placeholder('—'),
                        TextEntry::make('source')->label('Origine')->placeholder('—'),
                    ])->columns(2),
            ]);
    }
}
