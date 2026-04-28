<?php

namespace App\Filament\Resources\MenuPhotoImports\Pages;

use App\Filament\Resources\MenuPhotoImports\MenuPhotoImportResource;
use App\Models\MenuPhotoImport;
use App\Services\MenuImport\ApplyMenuDraftToMenu;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use JsonException;

class EditMenuPhotoImport extends EditRecord
{
    protected static string $resource = MenuPhotoImportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('applyDraft')
                ->label('Appliquer à la carte')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Ajouter ces entrées à la carte ?')
                ->modalDescription('De nouvelles catégories et plats seront créés en fin de liste (transaction).')
                ->visible(fn (): bool => $this->record->status === MenuPhotoImport::STATUS_COMPLETED)
                ->action(function (ApplyMenuDraftToMenu $apply): void {
                    $raw = $this->form->getState()['draft_json'] ?? $this->record->draft_json;

                    if (! is_string($raw) || trim($raw) === '') {
                        Notification::make()
                            ->title('Brouillon vide')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $draft = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
                    } catch (JsonException) {
                        Notification::make()
                            ->title('JSON invalide')
                            ->danger()
                            ->send();

                        return;
                    }

                    if (! is_array($draft)) {
                        Notification::make()
                            ->title('Format de brouillon incorrect')
                            ->danger()
                            ->send();

                        return;
                    }

                    try {
                        $count = $apply->apply(app('filament.restaurant'), $draft);
                    } catch (\InvalidArgumentException $e) {
                        Notification::make()
                            ->title('Validation du brouillon')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();

                        return;
                    }

                    $this->record->update(['draft_json' => $raw]);

                    Notification::make()
                        ->title('Carte mise à jour')
                        ->body($count.' plat(s) créé(s).')
                        ->success()
                        ->send();
                }),
            DeleteAction::make(),
        ];
    }
}
