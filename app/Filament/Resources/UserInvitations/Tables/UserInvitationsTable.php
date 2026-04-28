<?php

namespace App\Filament\Resources\UserInvitations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UserInvitationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')->label('E-mail')->searchable(),
                TextColumn::make('role')->label('Profil')->badge(),
                TextColumn::make('expires_at')->label('Expire le')->dateTime('d/m/Y H:i'),
                TextColumn::make('accepted_at')->label('Acceptée le')->dateTime('d/m/Y H:i')->placeholder('—'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                DeleteAction::make()->visible(fn ($record) => $record->accepted_at === null),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
