<?php

namespace App\Filament\Resources\SiteContents\Pages;

use App\Filament\Concerns\NormalizesPageSectionOrderFormState;
use App\Filament\Resources\SiteContents\SiteContentResource;
use App\Support\Filament\SiteContentRichEditorStateNormalizer;
use App\Support\SiteContent\SiteContentNormalizer;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;

class EditSiteContent extends EditRecord
{
    use NormalizesPageSectionOrderFormState;

    protected static string $resource = SiteContentResource::class;

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    /**
     * Le formulaire par défaut Filament utilise 2 colonnes (lg) : un seul bloc racine
     * (les onglets) ne remplit qu’une colonne → largeur ~50 %. Une colonne = pleine largeur.
     */
    public function defaultForm(Schema $schema): Schema
    {
        return parent::defaultForm($schema)->columns(1);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['content']) && is_array($data['content'])) {
            $data['content'] = SiteContentRichEditorStateNormalizer::normalize($data['content']);
        }

        return $this->inflatePageSectionOrderRepeaters($data);
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = $this->flattenPageSectionOrderRepeaters($data);

        if (isset($data['content']) && is_array($data['content'])) {
            $merged = SiteContentRichEditorStateNormalizer::normalize($data['content']);
            $data['content'] = SiteContentNormalizer::normalize($merged);
        }

        return $data;
    }
}
