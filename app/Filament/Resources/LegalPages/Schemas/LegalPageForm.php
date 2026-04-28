<?php

namespace App\Filament\Resources\LegalPages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LegalPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('slug')
                            ->label('Page')
                            ->options([
                                'mentions-legales' => 'Mentions légales (/legal/mentions-legales)',
                                'politique-de-confidentialite' => 'Politique de confidentialité',
                            ])
                            ->required()
                            ->disabledOn('edit'),
                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('body')
                            ->label('Texte')
                            ->required()
                            ->rows(18)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
