<?php

namespace App\Filament\Resources\SiteContents;

use App\Filament\Concerns\BelongsToCurrentRestaurant;
use App\Filament\Resources\SiteContents\Pages\CreateSiteContent;
use App\Filament\Resources\SiteContents\Pages\EditSiteContent;
use App\Filament\Resources\SiteContents\Pages\ListSiteContents;
use App\Filament\Resources\SiteContents\Schemas\SiteContentForm;
use App\Filament\Resources\SiteContents\Tables\SiteContentsTable;
use App\Models\RestaurantPageContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class SiteContentResource extends Resource
{
    use BelongsToCurrentRestaurant;

    protected static ?string $model = RestaurantPageContent::class;

    protected static ?string $navigationLabel = 'Contenus site';

    protected static ?string $modelLabel = 'contenu de page';

    protected static ?string $pluralModelLabel = 'contenus de page';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Site public';

    protected static ?int $navigationSort = 25;

    public static function form(Schema $schema): Schema
    {
        return SiteContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SiteContentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSiteContents::route('/'),
            'create' => CreateSiteContent::route('/create'),
            'edit' => EditSiteContent::route('/{record}/edit'),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
