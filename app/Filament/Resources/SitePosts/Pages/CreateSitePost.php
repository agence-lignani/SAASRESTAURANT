<?php

namespace App\Filament\Resources\SitePosts\Pages;

use App\Filament\Resources\SitePosts\SitePostResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateSitePost extends CreateRecord
{
    protected static string $resource = SitePostResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['restaurant_id'] = app('filament.restaurant')->id;

        if (blank($data['slug'] ?? null) && filled($data['title'] ?? null)) {
            $data['slug'] = Str::slug((string) $data['title']);
        }

        return $data;
    }
}
