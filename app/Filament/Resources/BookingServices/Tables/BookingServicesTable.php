<?php

namespace App\Filament\Resources\BookingServices\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class BookingServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Service')->searchable(),
                TextColumn::make('starts_at')->label('Début')->time('H:i'),
                TextColumn::make('ends_at')->label('Fin')->time('H:i'),
                TextColumn::make('capacity_covers')->label('Capacité')->sortable(),
                IconColumn::make('is_active')->label('Actif')->boolean(),
                TextColumn::make('sort_order')->label('Ordre')->sortable(),
            ])
            ->defaultSort('sort_order')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
