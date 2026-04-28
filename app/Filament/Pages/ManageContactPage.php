<?php

namespace App\Filament\Pages;

use App\Support\Filament\FilamentAccess;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ManageContactPage extends RestaurantSettingsPage
{
    public static function canAccess(): bool
    {
        return FilamentAccess::isOwner() || FilamentAccess::isReservation();
    }

    protected static ?string $navigationLabel = 'Coordonnées';

    protected static ?int $navigationSort = 30;

    protected static ?string $title = 'Coordonnées & pratique';

    public static function getSlug(?Panel $panel = null): string
    {
        return 'coordonnees';
    }

    protected function getFormRecord(): Model
    {
        return $this->restaurant();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Contact')
                    ->schema([
                        TextInput::make('contact_email')->label('E-mail')->email()->maxLength(255),
                        TextInput::make('contact_phone')->label('Téléphone')->tel()->maxLength(64),
                    ])->columns(2),
                Section::make('Adresse')
                    ->schema([
                        TextInput::make('address_line1')->label('Ligne 1')->maxLength(255),
                        TextInput::make('address_line2')->label('Ligne 2')->maxLength(255),
                        TextInput::make('postal_code')->label('Code postal')->maxLength(32),
                        TextInput::make('city')->label('Ville')->maxLength(128),
                        TextInput::make('country')->label('Pays (code ISO)')->length(2)->default('FR'),
                    ])->columns(2),
                Section::make('Infos pratiques')
                    ->schema([
                        Textarea::make('parking_info')->label('Parking')->rows(2),
                        Textarea::make('accessibility_info')->label('Accessibilité')->rows(2),
                    ]),
            ]);
    }
}
