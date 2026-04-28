<?php

namespace App\Filament\Resources\BookingSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookingSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slot_minutes')->label('Pas (min)'),
                TextColumn::make('min_notice_hours')->label('Délai min (h)'),
                TextColumn::make('max_days_ahead')->label('Max (jours)'),
                TextColumn::make('cancellation_hours')->label('Annulation (h)'),
                IconColumn::make('manual_confirmation_required')
                    ->label('Confirmation manuelle')
                    ->boolean(),
                TextColumn::make('external_integrations')
                    ->label('Intégrations actives')
                    ->formatStateUsing(function ($state): string {
                        if (! is_array($state)) {
                            return 'Aucune';
                        }

                        $enabled = collect($state)
                            ->filter(fn ($config): bool => is_array($config) && ($config['enabled'] ?? false))
                            ->keys()
                            ->map(fn (string $provider): string => match ($provider) {
                                'thefork' => 'TheFork',
                                'opentable' => 'OpenTable',
                                'zenchef' => 'Zenchef',
                                default => ucfirst($provider),
                            })
                            ->values()
                            ->all();

                        return $enabled === [] ? 'Aucune' : implode(', ', $enabled);
                    }),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
