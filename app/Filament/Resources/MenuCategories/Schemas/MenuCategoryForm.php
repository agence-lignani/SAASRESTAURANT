<?php

namespace App\Filament\Resources\MenuCategories\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Catégorie')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('Slug (optionnel)')
                            ->maxLength(255)
                            ->alphaDash(),
                        Textarea::make('description')
                            ->label('Description')
                            ->rows(2)
                            ->columnSpanFull(),
                        TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        TextInput::make('menu_pdf_url')
                            ->label('Lien PDF carte (optionnel)')
                            ->url()
                            ->maxLength(2048)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }
}
