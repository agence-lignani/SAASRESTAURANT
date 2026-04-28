<?php

namespace App\Filament\Resources\MenuCategories\RelationManagers;

use App\Support\AllergenCatalog;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MenuItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'menuItems';

    protected static ?string $title = 'Plats';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nom')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull(),
                TextInput::make('price')
                    ->label('Prix')
                    ->numeric()
                    ->step(0.01)
                    ->suffix('€'),
                TextInput::make('currency')
                    ->label('Devise')
                    ->default('EUR')
                    ->maxLength(3),
                TextInput::make('sort_order')
                    ->label('Ordre')
                    ->numeric()
                    ->default(0)
                    ->required(),
                Toggle::make('is_available')
                    ->label('Afficher / disponible')
                    ->default(true),
                CheckboxList::make('allergens')
                    ->label('Allergènes (F18)')
                    ->options(AllergenCatalog::options())
                    ->columns(2)
                    ->columnSpanFull(),
                CheckboxList::make('dietary_flags')
                    ->label('Régimes / signalétique')
                    ->options([
                        'vegetarian' => 'Végétarien',
                        'vegan' => 'Vegan',
                        'gluten_free' => 'Sans gluten',
                        'spicy' => 'Épicé',
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
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
            ->defaultSort('sort_order')
            ->headerActions([
                CreateAction::make(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
