<?php

namespace App\Filament\Resources\Reservations\Schemas;

use App\Models\Reservation;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ReservationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Réservation')
                    ->schema([
                        Select::make('booking_service_id')
                            ->label('Service')
                            ->relationship('bookingService', 'name', fn ($query) => $query->where('restaurant_id', app('filament.restaurant')->id))
                            ->required(),
                        DateTimePicker::make('reservation_at')->label('Date et heure')->seconds(false)->required(),
                        TextInput::make('covers')->label('Couverts')->numeric()->required()->minValue(1)->maxValue(40),
                        Select::make('status')
                            ->label('Statut')
                            ->options([
                                Reservation::STATUS_PENDING => 'En attente',
                                Reservation::STATUS_CONFIRMED => 'Confirmée',
                                Reservation::STATUS_DELAYED => 'Retard (nouvel horaire)',
                                Reservation::STATUS_ATTENDED => 'Présence confirmée',
                                Reservation::STATUS_REFUSED => 'Refusée',
                                Reservation::STATUS_CANCELLED => 'Annulée',
                                Reservation::STATUS_NO_SHOW => 'No-show',
                            ])
                            ->required(),
                        TextInput::make('customer_name')->label('Nom client')->required()->maxLength(255),
                        TextInput::make('customer_email')->label('Email client')->email()->required()->maxLength(255),
                        TextInput::make('customer_phone')->label('Téléphone')->maxLength(64),
                        Textarea::make('notes')->label('Notes')->rows(4)->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
