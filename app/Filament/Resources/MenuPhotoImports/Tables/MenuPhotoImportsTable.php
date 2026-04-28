<?php

namespace App\Filament\Resources\MenuPhotoImports\Tables;

use App\Models\MenuPhotoImport;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuPhotoImportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('#')->sortable(),
                TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        MenuPhotoImport::STATUS_COMPLETED => 'success',
                        MenuPhotoImport::STATUS_FAILED => 'danger',
                        MenuPhotoImport::STATUS_PROCESSING => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        MenuPhotoImport::STATUS_PENDING => 'En attente',
                        MenuPhotoImport::STATUS_PROCESSING => 'Traitement…',
                        MenuPhotoImport::STATUS_COMPLETED => 'Terminé',
                        MenuPhotoImport::STATUS_FAILED => 'Échec',
                        default => $state,
                    }),
                TextColumn::make('original_name')->label('Fichier')->limit(40)->toggleable(),
                TextColumn::make('processed_at')->label('Traité le')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('created_at')->label('Créé le')->dateTime('d/m/Y H:i')->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
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
