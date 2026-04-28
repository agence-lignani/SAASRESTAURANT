<?php

namespace App\Filament\Resources\SitePosts\Pages;

use App\Filament\Resources\SitePosts\SitePostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSitePost extends EditRecord
{
    protected static string $resource = SitePostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
