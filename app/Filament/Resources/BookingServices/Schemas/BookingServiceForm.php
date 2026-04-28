<?php

namespace App\Filament\Resources\BookingServices\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Service')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(100),
                        TimePicker::make('starts_at')
                            ->label('Début')
                            ->seconds(false)
                            ->required(),
                        TimePicker::make('ends_at')
                            ->label('Fin')
                            ->seconds(false)
                            ->required(),
                        TextInput::make('capacity_covers')
                            ->label('Capacité (couverts)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(400)
                            ->default(40)
                            ->required(),
                        Select::make('days_of_week')
                            ->label('Jours actifs')
                            ->multiple()
                            ->options([
                                1 => 'Lundi',
                                2 => 'Mardi',
                                3 => 'Mercredi',
                                4 => 'Jeudi',
                                5 => 'Vendredi',
                                6 => 'Samedi',
                                0 => 'Dimanche',
                            ])
                            ->helperText('Laisser vide pour tous les jours.'),
                        TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->required(),
                        Checkbox::make('is_active')
                            ->label('Service actif')
                            ->default(true),
                    ])->columns(2),
            ]);
    }
}
