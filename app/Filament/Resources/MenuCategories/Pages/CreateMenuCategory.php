<?php

namespace App\Filament\Resources\MenuCategories\Pages;

use App\Filament\Resources\MenuCategories\MenuCategoryResource;
use App\Models\MenuCategory;
use Filament\Resources\Pages\CreateRecord;

class CreateMenuCategory extends CreateRecord
{
    protected static string $resource = MenuCategoryResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['restaurant_id'] = app('filament.restaurant')->id;
        if (! isset($data['sort_order'])) {
            $data['sort_order'] = (int) MenuCategory::query()
                ->where('restaurant_id', $data['restaurant_id'])
                ->max('sort_order') + 1;
        }

        return $data;
    }
}
