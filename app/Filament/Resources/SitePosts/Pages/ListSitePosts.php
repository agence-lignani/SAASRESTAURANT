<?php

namespace App\Filament\Resources\SitePosts\Pages;

use App\Filament\Resources\SitePosts\SitePostResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSitePosts extends ListRecords
{
    protected static string $resource = SitePostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
