<?php

namespace App\Filament\Resources\MenuCategories;

use App\Filament\Concerns\BelongsToCurrentRestaurant;
use App\Filament\Resources\MenuCategories\Pages\CreateMenuCategory;
use App\Filament\Resources\MenuCategories\Pages\EditMenuCategory;
use App\Filament\Resources\MenuCategories\Pages\ListMenuCategories;
use App\Filament\Resources\MenuCategories\Pages\ViewMenuCategory;
use App\Filament\Resources\MenuCategories\RelationManagers\MenuItemsRelationManager;
use App\Filament\Resources\MenuCategories\Schemas\MenuCategoryForm;
use App\Filament\Resources\MenuCategories\Schemas\MenuCategoryInfolist;
use App\Filament\Resources\MenuCategories\Tables\MenuCategoriesTable;
use App\Models\MenuCategory;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MenuCategoryResource extends Resource
{
    use BelongsToCurrentRestaurant;

    protected static ?string $model = MenuCategory::class;

    protected static ?string $navigationLabel = 'Carte (catégories)';

    protected static ?string $modelLabel = 'catégorie';

    protected static ?string $pluralModelLabel = 'catégories';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Carte & médias';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return MenuCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return MenuCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenuCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MenuItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenuCategories::route('/'),
            'create' => CreateMenuCategory::route('/create'),
            'view' => ViewMenuCategory::route('/{record}'),
            'edit' => EditMenuCategory::route('/{record}/edit'),
        ];
    }
}
