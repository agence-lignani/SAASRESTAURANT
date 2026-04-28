<?php

namespace App\Filament\Resources\UserInvitations\Pages;

use App\Filament\Resources\UserInvitations\UserInvitationResource;
use App\Mail\UserInvitationMail;
use App\Models\User;
use App\Models\UserInvitation;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Throwable;

class CreateUserInvitation extends CreateRecord
{
    protected static string $resource = UserInvitationResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $restaurant = app('filament.restaurant');

        if (User::query()->where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'data.email' => 'Un compte existe déjà avec cet e-mail.',
            ]);
        }

        if (UserInvitation::query()
            ->where('restaurant_id', $restaurant->id)
            ->where('email', $data['email'])
            ->whereNull('accepted_at')
            ->where('expires_at', '>', now())
            ->exists()) {
            throw ValidationException::withMessages([
                'data.email' => 'Une invitation est déjà en cours pour cet e-mail.',
            ]);
        }

        $data['restaurant_id'] = $restaurant->id;
        $data['invited_by_user_id'] = auth()->id();
        $data['token'] = Str::random(64);
        $data['expires_at'] = now()->addDays(7);

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        $restaurant = app('filament.restaurant');
        $url = route('invitation.show', ['token' => $record->token]);

        // Après commit : l’invitation reste en base même si l’e-mail échoue (SMTP, etc.).
        DB::afterCommit(function () use ($record, $restaurant, $url): void {
            try {
                Mail::to($record->email)->send(new UserInvitationMail($record, $restaurant, $url));
            } catch (Throwable $e) {
                report($e);

                Notification::make()
                    ->title('Invitation enregistrée, mais l’e-mail n’a pas pu être envoyé.')
                    ->body('Vérifiez la configuration MAIL_* ou les journaux serveur.')
                    ->danger()
                    ->send();

                return;
            }

            Notification::make()
                ->title('Invitation envoyée')
                ->success()
                ->send();
        });
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        // Notification gérée dans afterCreate() (succès ou erreur d’envoi).
        return null;
    }
}
