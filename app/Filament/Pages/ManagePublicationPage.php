<?php

namespace App\Filament\Pages;

use App\Support\Filament\FilamentAccess;
use Filament\Forms\Components\Toggle;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ManagePublicationPage extends RestaurantSettingsPage
{
    public static function canAccess(): bool
    {
        return FilamentAccess::isOwner();
    }

    protected static ?string $navigationLabel = 'Publication';

    protected static ?int $navigationSort = 50;

    protected static ?string $title = 'Publication du site';

    public static function getSlug(?Panel $panel = null): string
    {
        return 'publication';
    }

    protected function getFormRecord(): Model
    {
        return $this->restaurant();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['is_published'] = $this->restaurant()->isPublished();

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $published = (bool) ($data['is_published'] ?? false);
        unset($data['is_published']);
        $data['published_at'] = $published ? now() : null;

        return $data;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Visibilité publique')
                    ->description('Si le site n’est pas publié, les visiteurs voient une page d’attente (503).')
                    ->schema([
                        Toggle::make('is_published')
                            ->label('Site public en ligne')
                            ->default(false),
                    ]),
            ]);
    }
}
