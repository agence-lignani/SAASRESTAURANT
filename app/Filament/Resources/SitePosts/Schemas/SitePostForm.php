<?php

namespace App\Filament\Resources\SitePosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SitePostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Article')
                    ->schema([
                        TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true),
                        TextInput::make('slug')
                            ->label('Fragment d’URL (slug)')
                            ->maxLength(255)
                            ->helperText('Laissez vide pour générer depuis le titre.'),
                        Textarea::make('excerpt')
                            ->label('Chapô')
                            ->rows(3)
                            ->maxLength(500),
                        Textarea::make('body')
                            ->label('Corps')
                            ->required()
                            ->rows(14)
                            ->columnSpanFull(),
                        DateTimePicker::make('published_at')
                            ->label('Publication le')
                            ->seconds(false)
                            ->native(false),
                    ]),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta titre')
                            ->maxLength(180),
                        TextInput::make('meta_description')
                            ->label('Meta description')
                            ->maxLength(320),
                    ])->columns(1),
            ]);
    }
}
