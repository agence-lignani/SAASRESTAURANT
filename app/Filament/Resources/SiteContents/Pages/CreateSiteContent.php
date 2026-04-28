<?php

namespace App\Filament\Resources\SiteContents\Pages;

use App\Filament\Concerns\NormalizesPageSectionOrderFormState;
use App\Filament\Resources\SiteContents\SiteContentResource;
use App\Support\Filament\SiteContentRichEditorStateNormalizer;
use App\Support\SiteContent\SiteContentDefaults;
use App\Support\SiteContent\SiteContentNormalizer;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class CreateSiteContent extends CreateRecord
{
    use NormalizesPageSectionOrderFormState;

    protected static string $resource = SiteContentResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function defaultForm(Schema $schema): Schema
    {
        return parent::defaultForm($schema)->columns(1);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $restaurant = app('filament.restaurant');
        $defaults = SiteContentDefaults::forRestaurant($restaurant);

        $data['restaurant_id'] = app('filament.restaurant')->id;
        $data = $this->flattenPageSectionOrderRepeaters($data);

        $merged = array_replace_recursive($defaults, $data['content'] ?? []);
        $merged = SiteContentRichEditorStateNormalizer::normalize($merged);
        $data['content'] = SiteContentNormalizer::normalize($merged);

        return $data;
    }
}
