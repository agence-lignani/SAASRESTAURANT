<?php

namespace App\Filament\Resources\SiteContents\Pages;

use App\Filament\Resources\SiteContents\SiteContentResource;
use App\Models\RestaurantPageContent;
use App\Support\SiteContent\SiteContentDefaults;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSiteContents extends ListRecords
{
    protected static string $resource = SiteContentResource::class;

    public function mount(): void
    {
        parent::mount();

        $record = RestaurantPageContent::query()
            ->where('restaurant_id', app('filament.restaurant')->id)
            ->first();

        if ($record) {
            $this->redirect(SiteContentResource::getUrl('edit', ['record' => $record]));

            return;
        }

        $restaurant = app('filament.restaurant');

        $record = RestaurantPageContent::query()->create([
            'restaurant_id' => $restaurant->id,
            'content' => SiteContentDefaults::forRestaurant($restaurant),
        ]);

        $this->redirect(SiteContentResource::getUrl('edit', ['record' => $record]));
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn (): bool => ! RestaurantPageContent::query()->where('restaurant_id', app('filament.restaurant')->id)->exists()),
        ];
    }
}
