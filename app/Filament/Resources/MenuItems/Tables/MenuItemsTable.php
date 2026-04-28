<?php

namespace App\Filament\Resources\MenuItems\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('menuCategory.name')->label('Catégorie')->sortable(),
                TextColumn::make('sort_order')->label('Ordre')->sortable(),
                TextColumn::make('name')->label('Plat')->searchable(),
                TextColumn::make('price')
                    ->label('Prix')
                    ->formatStateUsing(function ($state): string {
                        if ($state === null || $state === '') {
                            return '—';
                        }

                        return number_format((float) $state, 2, ',', ' ').' €';
                    }),
                IconColumn::make('is_available')->label('Actif')->boolean(),
            ])
            ->defaultSort('menu_category_id')
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
