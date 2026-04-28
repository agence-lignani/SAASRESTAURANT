<?php

namespace App\Filament\Resources\UserInvitations\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInvitationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Invitation')
                    ->schema([
                        TextInput::make('email')
                            ->label('E-mail')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Select::make('role')
                            ->label('Profil Filament')
                            ->options([
                                'owner' => 'Gérant / propriétaire',
                                'reservation' => 'Gestionnaire réservations',
                                'editor' => 'Rédacteur contenu',
                                'server' => 'Serveur (consultation réservations)',
                            ])
                            ->required(),
                    ]),
            ]);
    }
}
