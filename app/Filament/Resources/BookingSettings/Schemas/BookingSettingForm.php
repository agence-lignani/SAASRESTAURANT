<?php

namespace App\Filament\Resources\BookingSettings\Schemas;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BookingSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Règles de réservation')
                    ->schema([
                        TextInput::make('slot_minutes')->label('Pas créneau (minutes)')->numeric()->default(30)->minValue(5)->maxValue(120)->required(),
                        TextInput::make('min_notice_hours')->label('Délai min (heures)')->numeric()->default(2)->minValue(0)->maxValue(72)->required(),
                        TextInput::make('max_days_ahead')->label('Réservation max (jours)')->numeric()->default(30)->minValue(1)->maxValue(365)->required(),
                        TextInput::make('cancellation_hours')->label('Annulation client jusqu’à (heures)')->numeric()->default(6)->minValue(0)->maxValue(168)->required(),
                        Checkbox::make('allow_client_cancellation')->label('Autoriser annulation client')->default(true),
                        Checkbox::make('manual_confirmation_required')->label('Réservations à confirmer manuellement')->helperText('Si désactivé, les réservations sont confirmées automatiquement.')->default(false),
                    ])->columns(2),
                Section::make('Rappel client avant le service (M6 / J7)')
                    ->description('E-mail automatique pour les réservations déjà confirmées. Planifié côté serveur (cron / worker) via la commande reservations:send-reminders.')
                    ->schema([
                        Checkbox::make('reminder_enabled')->label('Activer le rappel par e-mail')->default(false),
                        TextInput::make('reminder_hours_before')
                            ->label('Envoyer le rappel (heures avant le service)')
                            ->numeric()
                            ->default(24)
                            ->minValue(1)
                            ->maxValue(168)
                            ->required()
                            ->helperText('Ex. 24 = la veille au plus tard dans la fenêtre d’exécution du job (toutes les 15 minutes en production).'),
                    ])->columns(2),
                Section::make('Notifications équipe')
                    ->schema([
                        Repeater::make('notification_emails')
                            ->label('Adresses e-mail')
                            ->simple(
                                TextInput::make('email')->email()->required(),
                            )
                            ->helperText('Si vide, l’e-mail de contact du restaurant est utilisé.'),
                    ]),
                Section::make('Intégrations externes')
                    ->description('Activez uniquement les plateformes utilisées par le restaurant. La synchronisation ignore les plateformes désactivées.')
                    ->schema([
                        Grid::make(1)->schema([
                            self::integrationFields('thefork', 'TheFork'),
                            self::integrationFields('opentable', 'OpenTable'),
                            self::integrationFields('zenchef', 'Zenchef'),
                        ]),
                    ]),
            ]);
    }

    private static function integrationFields(string $key, string $label): Section
    {
        return Section::make($label)
            ->schema([
                Checkbox::make("external_integrations.{$key}.enabled")
                    ->label("Activer {$label}"),
                TextInput::make("external_integrations.{$key}.restaurant_reference")
                    ->label('Référence restaurant')
                    ->maxLength(120),
                TextInput::make("external_integrations.{$key}.api_key")
                    ->label('Clé API')
                    ->password()
                    ->revealable()
                    ->maxLength(255),
            ])
            ->columns(3);
    }
}
