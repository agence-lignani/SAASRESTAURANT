<?php

namespace App\Filament\Resources\MenuItems\Schemas;

use App\Models\MenuCategory;
use App\Support\AllergenCatalog;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Plat')
                    ->schema([
                        Select::make('menu_category_id')
                            ->label('Catégorie')
                            ->options(fn (): array => MenuCategory::query()
                                ->where('restaurant_id', app('filament.restaurant')->id)
                                ->orderBy('sort_order')
                                ->pluck('name', 'id')
                                ->all())
                            ->required()
                            ->searchable(),
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
                            ->label('Allergènes')
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
                    ])->columns(2),
            ]);
    }
}
