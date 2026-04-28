<?php

namespace App\Filament\Resources\GalleryMedia;

use App\Filament\Concerns\BelongsToCurrentRestaurant;
use App\Filament\Resources\GalleryMedia\Pages\CreateGalleryMedia;
use App\Filament\Resources\GalleryMedia\Pages\EditGalleryMedia;
use App\Filament\Resources\GalleryMedia\Pages\ListGalleryMedia;
use App\Filament\Resources\GalleryMedia\Schemas\GalleryMediaForm;
use App\Filament\Resources\GalleryMedia\Tables\GalleryMediaTable;
use App\Models\GalleryMedia;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class GalleryMediaResource extends Resource
{
    use BelongsToCurrentRestaurant;

    protected static ?string $model = GalleryMedia::class;

    protected static ?string $navigationLabel = 'Galerie';

    protected static ?string $modelLabel = 'média';

    protected static ?string $pluralModelLabel = 'médias galerie';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCamera;

    protected static string|UnitEnum|null $navigationGroup = 'Carte & médias';

    protected static ?int $navigationSort = 33;

    public static function form(Schema $schema): Schema
    {
        return GalleryMediaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GalleryMediaTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListGalleryMedia::route('/'),
            'create' => CreateGalleryMedia::route('/create'),
            'edit' => EditGalleryMedia::route('/{record}/edit'),
        ];
    }
}
