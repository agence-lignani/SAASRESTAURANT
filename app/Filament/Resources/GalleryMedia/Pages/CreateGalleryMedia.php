<?php

namespace App\Filament\Resources\GalleryMedia\Pages;

use App\Filament\Resources\GalleryMedia\GalleryMediaResource;
use App\Models\GalleryMedia;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateGalleryMedia extends CreateRecord
{
    protected static string $resource = GalleryMediaResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $restaurantId = app('filament.restaurant')->id;
        $maxItems = (int) config('gallery.max_items_per_restaurant', 0);

        if ($maxItems > 0) {
            $current = GalleryMedia::query()->where('restaurant_id', $restaurantId)->count();
            if ($current >= $maxItems) {
                throw ValidationException::withMessages([
                    'path' => "Limite de {$maxItems} photos atteinte pour cet établissement. Supprimez des médias ou augmentez GALLERY_MAX_ITEMS (configuration).",
                ]);
            }
        }

        $data['restaurant_id'] = $restaurantId;
        $data['disk'] = 'public';

        return $data;
    }
}
