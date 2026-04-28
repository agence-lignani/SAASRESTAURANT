<?php

namespace App\Filament\Pages;

use App\Models\OpeningHour;
use App\Models\Restaurant;
use App\Support\Filament\FilamentAccess;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\CanUseDatabaseTransactions;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use UnitEnum;

class ManageOpeningHoursPage extends Page
{
    use CanUseDatabaseTransactions;

    public static function canAccess(): bool
    {
        return FilamentAccess::isOwner() || FilamentAccess::isReservation();
    }

    protected string $view = 'filament-panels::pages.page';

    protected static string|UnitEnum|null $navigationGroup = 'Réservations';

    protected static ?string $navigationLabel = 'Horaires & fermetures';

    protected static ?int $navigationSort = 60;

    protected static ?string $title = 'Horaires & fermetures exceptionnelles';

    public ?array $data = [];

    public static function getSlug(?Panel $panel = null): string
    {
        return 'horaires';
    }

    protected function restaurant(): Restaurant
    {
        /** @var Restaurant */
        return app('filament.restaurant');
    }

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $r = $this->restaurant();
        $r->load(['openingHours', 'openingHourExceptions']);

        $days = [];
        foreach (range(0, 6) as $d) {
            $oh = $r->openingHours->firstWhere('day_of_week', $d);
            $days[$d] = [
                'is_closed' => $oh ? (bool) $oh->is_closed : ($d === 0),
                'opens_at' => $oh?->opens_at,
                'closes_at' => $oh?->closes_at,
            ];
        }

        $exceptions = $r->openingHourExceptions->map(fn (Model $e): array => [
            'exception_date' => $e->exception_date->format('Y-m-d'),
            'is_closed' => (bool) $e->is_closed,
            'opens_at' => $e->opens_at,
            'closes_at' => $e->closes_at,
            'note' => $e->note,
        ])->values()->all();

        $this->form->fill([
            'days' => $days,
            'exceptions' => $exceptions,
        ]);
    }

    public function save(): void
    {
        try {
            $this->beginDatabaseTransaction();

            $data = $this->form->getState();
            $r = $this->restaurant();

            foreach (range(0, 6) as $d) {
                $row = $data['days'][$d] ?? [];
                OpeningHour::query()->updateOrCreate(
                    [
                        'restaurant_id' => $r->id,
                        'day_of_week' => $d,
                    ],
                    [
                        'is_closed' => (bool) ($row['is_closed'] ?? true),
                        'opens_at' => empty($row['opens_at']) ? null : $row['opens_at'],
                        'closes_at' => empty($row['closes_at']) ? null : $row['closes_at'],
                    ]
                );
            }

            $r->openingHourExceptions()->delete();
            foreach ($data['exceptions'] ?? [] as $row) {
                if (empty($row['exception_date'])) {
                    continue;
                }
                $r->openingHourExceptions()->create([
                    'exception_date' => $row['exception_date'],
                    'is_closed' => (bool) ($row['is_closed'] ?? false),
                    'opens_at' => empty($row['opens_at']) ? null : $row['opens_at'],
                    'closes_at' => empty($row['closes_at']) ? null : $row['closes_at'],
                    'note' => $row['note'] ?? null,
                ]);
            }

            $this->commitDatabaseTransaction();
        } catch (Throwable $exception) {
            $this->rollBackDatabaseTransaction();

            throw $exception;
        }

        Notification::make()
            ->title('Horaires enregistrés')
            ->success()
            ->send();
    }

    public function defaultForm(Schema $schema): Schema
    {
        return $schema->statePath('data');
    }

    public function form(Schema $schema): Schema
    {
        $sections = [];

        foreach ($this->dayLabels() as $d => $label) {
            $sections[] = Section::make($label)
                ->schema([
                    Toggle::make("days.{$d}.is_closed")
                        ->label('Fermé')
                        ->live(),
                    TimePicker::make("days.{$d}.opens_at")
                        ->label('Ouverture')
                        ->seconds(false)
                        ->hidden(fn (Get $get): bool => (bool) $get("days.{$d}.is_closed")),
                    TimePicker::make("days.{$d}.closes_at")
                        ->label('Fermeture')
                        ->seconds(false)
                        ->hidden(fn (Get $get): bool => (bool) $get("days.{$d}.is_closed")),
                ])
                ->columns(3);
        }

        $sections[] = Section::make('Fermetures exceptionnelles')
            ->description('Ajoutez ici les jours fériés, fermetures exceptionnelles ou horaires spéciaux.')
            ->schema([
                Repeater::make('exceptions')
                    ->schema([
                        DatePicker::make('exception_date')
                            ->label('Date')
                            ->required()
                            ->native(false),
                        Toggle::make('is_closed')
                            ->label('Fermé toute la journée')
                            ->default(false)
                            ->live(),
                        TimePicker::make('opens_at')
                            ->seconds(false)
                            ->hidden(fn (Get $get): bool => (bool) $get('is_closed')),
                        TimePicker::make('closes_at')
                            ->seconds(false)
                            ->hidden(fn (Get $get): bool => (bool) $get('is_closed')),
                        TextInput::make('note')->label('Note')->maxLength(255),
                    ])
                    ->columns(2)
                    ->default([]),
            ]);

        return $schema->components($sections);
    }

    /**
     * @return array<int, string>
     */
    protected function dayLabels(): array
    {
        return [
            0 => 'Dimanche',
            1 => 'Lundi',
            2 => 'Mardi',
            3 => 'Mercredi',
            4 => 'Jeudi',
            5 => 'Vendredi',
            6 => 'Samedi',
        ];
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
                Actions::make([
                    Action::make('save')
                        ->label('Enregistrer')
                        ->submit('save')
                        ->keyBindings(['mod+s']),
                ])
                    ->alignment(Alignment::Start)
                    ->key('form-actions'),
            ]);
    }
}
