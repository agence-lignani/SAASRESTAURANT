<?php

namespace App\Filament\Pages;

use App\Models\Restaurant;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use UnitEnum;

/**
 * Formulaire Filament lié à un enregistrement (établissement ou sous-ressource).
 */
abstract class RestaurantSettingsPage extends Page
{
    use CanUseDatabaseTransactions;

    protected string $view = 'filament-panels::pages.page';

    protected static string|UnitEnum|null $navigationGroup = 'Site vitrine';

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function restaurant(): Restaurant
    {
        /** @var Restaurant */
        return app('filament.restaurant');
    }

    protected function fillForm(): void
    {
        $record = $this->getFormRecord();
        $data = $record->toArray();
        $this->form->fill($this->mutateFormDataBeforeFill($data));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        return $data;
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();
            $data = $this->mutateFormDataBeforeSave($data);

            $record = $this->getFormRecord();
            $record->fill($data);
            $record->save();

            $this->commitDatabaseTransaction();
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        Notification::make()
            ->title('Enregistré')
            ->success()
            ->send();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $data;
    }

    abstract protected function getFormRecord(): Model;

    abstract public function form(Schema $schema): Schema;

    public function defaultForm(Schema $schema): Schema
    {
        return $schema
            ->operation('edit')
            ->model($this->getFormRecord())
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getFormContentComponent(),
            ]);
    }

    public function getFormContentComponent(): Component
    {
        return Form::make([EmbeddedSchema::make('form')])
            ->id('form')
            ->livewireSubmitHandler('save')
            ->footer([
                Actions::make($this->getFormActions())
                    ->alignment(Alignment::Start)
                    ->fullWidth($this->hasFullWidthFormActions())
                    ->key('form-actions'),
            ]);
    }

    /**
     * @return array<Action | \Filament\Actions\ActionGroup>
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Enregistrer')
                ->submit('save')
                ->keyBindings(['mod+s']),
        ];
    }

    protected function hasFullWidthFormActions(): bool
    {
        return false;
    }
}
