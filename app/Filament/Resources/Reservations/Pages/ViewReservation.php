<?php

namespace App\Filament\Resources\Reservations\Pages;

use App\Filament\Resources\Reservations\ReservationResource;
use App\Models\Reservation;
use App\Support\Filament\FilamentAccess;
use Carbon\CarbonImmutable;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\Size;
use Filament\Support\Enums\Width;

class ViewReservation extends ViewRecord
{
    protected static string $resource = ReservationResource::class;

    protected function getHeaderActions(): array
    {
        $edit = EditAction::make()
            ->visible(fn (): bool => auth()->user()?->can('update', $this->record) ?? false);

        if (! FilamentAccess::isServer()) {
            return [$edit];
        }

        $canFloor = (auth()->user()?->can('delayReservation', $this->record) ?? false)
            || (auth()->user()?->can('confirmPresence', $this->record) ?? false)
            || (auth()->user()?->can('cancelReservation', $this->record) ?? false);

        if (! $canFloor) {
            return [];
        }

        $delay = Action::make('delayReservation')
            ->label('Retard')
            ->icon('heroicon-o-clock')
            ->color('warning')
            ->authorize('delayReservation')
            ->size(Size::Large)
            ->modalHeading('Reporter le créneau')
            ->modalDescription('Le statut passera à « Retard » et l’horaire affiché sera mis à jour. Une trace est ajoutée dans les notes internes.')
            ->modalWidth(Width::Large)
            ->slideOver()
            ->schema([
                DateTimePicker::make('new_reservation_at')
                    ->label('Nouvelle date et heure')
                    ->seconds(false)
                    ->required()
                    ->default(fn (): ?CarbonImmutable => $this->record->reservation_at?->toImmutable())
                    ->timezone(config('app.timezone'))
                    ->columnSpanFull(),
            ])
            ->action(function (array $data): void {
                $reservation = $this->record;
                $previous = $reservation->reservation_at?->toImmutable();
                $next = CarbonImmutable::parse($data['new_reservation_at'], config('app.timezone'));

                $line = '[Salle — retard] Créneau reporté du '
                    .$previous?->format('d/m/Y à H:i')
                    .' au '.$next->format('d/m/Y à H:i').'.';
                $notes = trim((string) $reservation->notes);
                $reservation->notes = $notes === '' ? $line : $notes."\n".$line;

                $reservation->status = Reservation::STATUS_DELAYED;
                $reservation->reservation_at = $next;
                $reservation->save();

                $reservation->refresh();

                Notification::make()
                    ->title('Créneau mis à jour')
                    ->success()
                    ->send();
            });

        $confirm = Action::make('confirmPresence')
            ->label('Présence')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->authorize('confirmPresence')
            ->size(Size::Large)
            ->requiresConfirmation()
            ->modalHeading('Confirmer la présence du client ?')
            ->modalDescription('Indique que la table a été honorée (fin du suivi salle pour cette réservation).')
            ->modalWidth(Width::Medium)
            ->action(function (): void {
                $this->record->status = Reservation::STATUS_ATTENDED;
                $this->record->save();
                $this->record->refresh();

                Notification::make()
                    ->title('Présence enregistrée')
                    ->success()
                    ->send();
            });

        $cancel = Action::make('cancelReservation')
            ->label('Annuler')
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->authorize('cancelReservation')
            ->size(Size::Large)
            ->requiresConfirmation()
            ->modalHeading('Annuler cette réservation ?')
            ->modalDescription('Le client recevra l’e-mail d’annulation habituel si votre configuration l’envoie.')
            ->modalWidth(Width::Medium)
            ->action(function (): void {
                $this->record->status = Reservation::STATUS_CANCELLED;
                $this->record->save();
                $this->record->refresh();

                Notification::make()
                    ->title('Réservation annulée')
                    ->warning()
                    ->send();
            });

        return [
            ActionGroup::make([$delay, $confirm, $cancel])
                ->buttonGroup()
                ->extraAttributes(['class' => 'rsv-floor-action-group']),
        ];
    }
}
