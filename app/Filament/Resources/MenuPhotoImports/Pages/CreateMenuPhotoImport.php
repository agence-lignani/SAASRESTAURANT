<?php

namespace App\Filament\Resources\MenuPhotoImports\Pages;

use App\Filament\Resources\MenuPhotoImports\MenuPhotoImportResource;
use App\Jobs\ProcessMenuPhotoImport;
use App\Models\MenuPhotoImport;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class CreateMenuPhotoImport extends CreateRecord
{
    protected static string $resource = MenuPhotoImportResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $photo = $data['photo'] ?? null;
        unset($data['photo']);

        if (is_array($photo)) {
            $photo = Arr::first($photo);
        }

        $diskName = 'public';
        $disk = Storage::disk($diskName);

        $data['restaurant_id'] = app('filament.restaurant')->id;
        $data['disk'] = $diskName;
        $data['path'] = $photo;
        $data['status'] = MenuPhotoImport::STATUS_PENDING;
        $data['original_name'] = $photo ? basename((string) $photo) : null;

        if (is_string($photo) && $photo !== '' && $disk->exists($photo)) {
            $data['size'] = $disk->size($photo);
            $data['mime'] = $disk->mimeType($photo);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        ProcessMenuPhotoImport::dispatch($this->record);

        $isStub = config('menu_import.driver', 'tesseract') === 'stub';

        Notification::make()
            ->title($isStub ? 'Brouillon démo' : 'Brouillon généré (OCR)')
            ->body(
                $isStub
                    ? 'Mode stub : contenu d’exemple fixe. Passez MENU_IMPORT_DRIVER=tesseract pour lire la photo avec Tesseract.'
                    : 'Texte extrait de l’image via Tesseract. Contrôlez le JSON (fautes OCR, prix) avant d’appliquer à la carte.'
            )
            ->success()
            ->send();
    }
}
