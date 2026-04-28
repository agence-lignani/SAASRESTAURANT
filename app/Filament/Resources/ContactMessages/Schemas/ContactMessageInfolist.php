<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use App\Models\ContactMessage;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ContactMessageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Message')
                    ->schema([
                        TextEntry::make('created_at')->label('Reçu le')->dateTime('d/m/Y H:i'),
                        TextEntry::make('name')->label('Nom'),
                        TextEntry::make('email')->label('E-mail')->copyable(),
                        TextEntry::make('phone')->label('Téléphone')->placeholder('—'),
                        TextEntry::make('subject')
                            ->label('Sujet')
                            ->formatStateUsing(fn (string $state): string => ContactMessage::subjectOptions()[$state] ?? $state),
                        TextEntry::make('body')->label('Message')->columnSpanFull(),
                        TextEntry::make('ip_address')->label('IP')->placeholder('—'),
                        TextEntry::make('read_at')->label('Lu le')->dateTime('d/m/Y H:i')->placeholder('—'),
                    ])->columns(2),
            ]);
    }
}
