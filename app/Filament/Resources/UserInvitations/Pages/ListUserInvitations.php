<?php

namespace App\Filament\Resources\UserInvitations\Pages;

use App\Filament\Resources\UserInvitations\UserInvitationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserInvitations extends ListRecords
{
    protected static string $resource = UserInvitationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Pas de modale : le CreateAction par défaut n’exécute pas CreateUserInvitation
            // (token, restaurant_id, e-mail, etc.) et provoque une sauvegarde incomplète.
            CreateAction::make()
                ->modal(false)
                ->url(fn (): string => UserInvitationResource::getUrl('create')),
        ];
    }
}
