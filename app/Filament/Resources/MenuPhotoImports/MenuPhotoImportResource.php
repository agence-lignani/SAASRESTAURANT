<?php

namespace App\Filament\Resources\MenuPhotoImports;

use App\Filament\Concerns\BelongsToCurrentRestaurant;
use App\Filament\Resources\MenuPhotoImports\Pages\CreateMenuPhotoImport;
use App\Filament\Resources\MenuPhotoImports\Pages\EditMenuPhotoImport;
use App\Filament\Resources\MenuPhotoImports\Pages\ListMenuPhotoImports;
use App\Filament\Resources\MenuPhotoImports\Schemas\MenuPhotoImportForm;
use App\Filament\Resources\MenuPhotoImports\Tables\MenuPhotoImportsTable;
use App\Models\MenuPhotoImport;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class MenuPhotoImportResource extends Resource
{
    use BelongsToCurrentRestaurant;

    protected static ?string $model = MenuPhotoImport::class;

    protected static ?string $navigationLabel = 'Import photo (carte)';

    protected static ?string $modelLabel = 'import photo';

    protected static ?string $pluralModelLabel = 'imports photo';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Carte & médias';

    protected static ?int $navigationSort = 32;

    public static function form(Schema $schema): Schema
    {
        return MenuPhotoImportForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenuPhotoImportsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenuPhotoImports::route('/'),
            'create' => CreateMenuPhotoImport::route('/create'),
            'edit' => EditMenuPhotoImport::route('/{record}/edit'),
        ];
    }
}
