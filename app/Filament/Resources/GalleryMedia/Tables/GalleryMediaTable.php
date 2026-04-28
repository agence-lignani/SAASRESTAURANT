<?php

namespace App\Filament\Resources\GalleryMedia\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GalleryMediaTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('path')
                    ->label('Aperçu')
                    ->disk(fn ($record) => $record->disk)
                    ->square()
                    ->size(48),
                TextColumn::make('sort_order')->label('Ordre')->sortable(),
                TextColumn::make('caption')->label('Légende')->limit(40)->searchable(),
                TextColumn::make('alt_text')->label('Alt')->limit(30)->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('MàJ')->dateTime('d/m/Y')->sortable()->toggleable(isToggledHiddenByDefault: true),
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
