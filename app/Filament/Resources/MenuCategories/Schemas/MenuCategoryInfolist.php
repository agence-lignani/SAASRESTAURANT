<?php

namespace App\Filament\Resources\MenuCategories\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuCategoryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextEntry::make('name')->label('Nom'),
                        TextEntry::make('slug')->label('Slug')->placeholder('—'),
                        TextEntry::make('description')->label('Description')->placeholder('—'),
                        TextEntry::make('sort_order')->label('Ordre'),
                        TextEntry::make('menu_pdf_url')->label('PDF')->url(fn (?string $state): ?string => $state)->placeholder('—'),
                    ])->columns(2),
            ]);
    }
}
