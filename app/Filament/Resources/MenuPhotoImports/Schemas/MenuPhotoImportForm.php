<?php

namespace App\Filament\Resources\MenuPhotoImports\Schemas;

use App\Filament\Support\FilamentImageUpload;
use App\Models\MenuPhotoImport;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class MenuPhotoImportForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Fichier')
                    ->description('Par défaut, le texte est extrait de la photo avec Tesseract (OCR). Prérequis : binaire « tesseract » installé + langues fra/eng (ex. brew install tesseract tesseract-lang). Variable MENU_IMPORT_DRIVER=stub pour désactiver l’OCR (brouillon démo).')
                    ->schema([
                        FilamentImageUpload::withAutomaticCompression(
                            FileUpload::make('photo')
                                ->label('Image de la carte')
                                ->disk('public')
                                ->directory(fn (): string => 'menu-imports/'.app('filament.restaurant')->id)
                                ->image()
                                ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                ->helperText('Les images lourdes sont compressées automatiquement pour respecter le plafond du site.')
                                ->required()
                                ->columnSpanFull()
                                ->visibleOn('create')
                        ),
                    ]),
                Section::make('État')
                    ->schema([
                        TextInput::make('status')
                            ->label('Statut')
                            ->disabled()
                            ->dehydrated(false)
                            ->visibleOn('edit'),
                        TextInput::make('error_message')
                            ->label('Message d’erreur')
                            ->disabled()
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->visibleOn('edit')
                            ->visible(fn (?MenuPhotoImport $record): bool => $record?->status === MenuPhotoImport::STATUS_FAILED),
                        Textarea::make('draft_json')
                            ->label('Brouillon JSON')
                            ->helperText('Si extraction_mode = ocr_tesseract, relisez le texte (meta.ocr_preview). Si stub_no_ocr, c’est le mode démo — éditez le JSON avant « Appliquer ».')
                            ->rows(20)
                            ->columnSpanFull()
                            ->formatStateUsing(function (?string $state): string {
                                if ($state === null || $state === '') {
                                    return '';
                                }

                                try {
                                    $decoded = json_decode($state, true, 512, JSON_THROW_ON_ERROR);
                                } catch (\JsonException) {
                                    return $state;
                                }

                                return is_array($decoded)
                                    ? (string) json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)
                                    : $state;
                            })
                            ->visibleOn('edit')
                            ->visible(fn (?MenuPhotoImport $record): bool => $record && in_array($record->status, [
                                MenuPhotoImport::STATUS_COMPLETED,
                                MenuPhotoImport::STATUS_FAILED,
                            ], true)),
                    ]),
            ]);
    }
}
