<?php

namespace App\Filament\Resources\MenuPhotoImports\Pages;

use App\Filament\Resources\MenuPhotoImports\MenuPhotoImportResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListMenuPhotoImports extends ListRecords
{
    protected static string $resource = MenuPhotoImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
