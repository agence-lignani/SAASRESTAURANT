<?php

namespace App\Filament\Resources\SitePosts;

use App\Filament\Concerns\BelongsToCurrentRestaurant;
use App\Filament\Resources\SitePosts\Pages\CreateSitePost;
use App\Filament\Resources\SitePosts\Pages\EditSitePost;
use App\Filament\Resources\SitePosts\Pages\ListSitePosts;
use App\Filament\Resources\SitePosts\Schemas\SitePostForm;
use App\Filament\Resources\SitePosts\Tables\SitePostsTable;
use App\Models\SitePost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class SitePostResource extends Resource
{
    use BelongsToCurrentRestaurant;

    protected static ?string $model = SitePost::class;

    protected static ?string $navigationLabel = 'Actualités';

    protected static ?string $modelLabel = 'article';

    protected static ?string $pluralModelLabel = 'articles';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedNewspaper;

    protected static string|UnitEnum|null $navigationGroup = 'Site public';

    protected static ?int $navigationSort = 35;

    public static function form(Schema $schema): Schema
    {
        return SitePostForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SitePostsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSitePosts::route('/'),
            'create' => CreateSitePost::route('/create'),
            'edit' => EditSitePost::route('/{record}/edit'),
        ];
    }
}
