<?php

namespace App\Filament\Resources\GalleryMedia\Pages;

use App\Filament\Resources\GalleryMedia\GalleryMediaResource;
use App\Models\GalleryMedia;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;

class ListGalleryMedia extends ListRecords
{
    protected static string $resource = GalleryMediaResource::class;

    public function getSubheading(): string|Htmlable|null
    {
        $restaurant = app('filament.restaurant');
        $count = GalleryMedia::query()->where('restaurant_id', $restaurant->id)->count();
        $maxKb = (int) config('gallery.max_upload_kb', 500);
        $formats = (string) config('gallery.accepted_extensions_label', 'JPEG, PNG, WebP');
        $maxItems = (int) config('gallery.max_items_per_restaurant', 0);

        $parts = [
            "Formats : {$formats}. Compression automatique pour viser au plus ~{$maxKb} Ko par fichier.",
        ];

        if ($maxItems > 0) {
            $parts[] = "Limite : {$count} / {$maxItems} photos pour cet établissement.";
        } else {
            $parts[] = "{$count} photo(s) publiée(s).";
        }

        return implode(' ', $parts);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->disabled(function (): bool {
                    $max = (int) config('gallery.max_items_per_restaurant', 0);
                    if ($max <= 0) {
                        return false;
                    }

                    return GalleryMedia::query()
                        ->where('restaurant_id', app('filament.restaurant')->id)
                        ->count() >= $max;
                }),
        ];
    }
}
