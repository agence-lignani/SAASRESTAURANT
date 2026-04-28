<?php

namespace App\Filament\Resources\GalleryMedia\Schemas;

use App\Filament\Support\FilamentImageUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class GalleryMediaForm
{
    public static function configure(Schema $schema): Schema
    {
        $maxKb = (int) config('gallery.max_upload_kb', 500);
        $maxClientKb = (int) config('image_upload.max_client_upload_kilobytes', 10240);
        $formats = (string) config('gallery.accepted_extensions_label', 'JPEG, PNG, WebP');

        return $schema
            ->components([
                Section::make('Média')
                    ->description("Performance du site : chaque image est compressée automatiquement pour viser au plus ~{$maxKb} Ko (fichiers jusqu’à ~{$maxClientKb} Ko acceptés avant compression). Formats {$formats} uniquement. La limite du nombre de photos est indiquée sur la liste de la galerie.")
                    ->schema([
                        FilamentImageUpload::withAutomaticCompression(
                            FileUpload::make('path')
                                ->label('Image')
                                ->disk('public')
                                ->directory(fn (): string => 'gallery/'.app('filament.restaurant')->id)
                                ->image()
                                ->acceptedFileTypes(config('gallery.accepted_mime_types', ['image/jpeg', 'image/png', 'image/webp']))
                                ->required(fn (mixed $livewire): bool => $livewire instanceof CreateRecord)
                                ->columnSpanFull()
                        ),
                        TextInput::make('caption')
                            ->label('Légende')
                            ->maxLength(255),
                        TextInput::make('alt_text')
                            ->label('Texte alternatif')
                            ->maxLength(255)
                            ->helperText('Accessibilité : décrivez brièvement l’image.'),
                        TextInput::make('sort_order')
                            ->label('Ordre')
                            ->numeric()
                            ->default(0)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
