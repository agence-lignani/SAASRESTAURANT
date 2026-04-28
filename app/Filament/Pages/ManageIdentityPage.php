<?php

namespace App\Filament\Pages;

use App\Support\Filament\FilamentAccess;
use Filament\Forms\Components\TextInput;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ManageIdentityPage extends RestaurantSettingsPage
{
    public static function canAccess(): bool
    {
        return FilamentAccess::isOwner();
    }

    protected static ?string $navigationLabel = 'Identité & marque';

    protected static ?int $navigationSort = 10;

    protected static ?string $title = 'Identité & marque';

    public static function getSlug(?Panel $panel = null): string
    {
        return 'identite';
    }

    protected function getFormRecord(): Model
    {
        return $this->restaurant();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Établissement')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom commercial')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('Identifiant URL (slug)')
                            ->required()
                            ->maxLength(255)
                            ->alphaDash()
                            ->rules([
                                fn (): Unique => Rule::unique('restaurants', 'slug')
                                    ->ignore($this->restaurant()->id),
                            ]),
                        TextInput::make('tagline')
                            ->label('Slogan / accroche')
                            ->maxLength(255),
                        TextInput::make('public_host')
                            ->label('Nom d’hôte public (optionnel)')
                            ->helperText('Ex. restaurant.example.com — en local laisser vide pour utiliser le premier établissement.')
                            ->maxLength(255),
                    ]),
                Section::make('Réseaux sociaux')
                    ->schema([
                        TextInput::make('facebook_url')->label('Facebook')->url()->maxLength(500),
                        TextInput::make('instagram_url')->label('Instagram')->url()->maxLength(500),
                        TextInput::make('twitter_url')->label('X / Twitter')->url()->maxLength(500),
                        TextInput::make('linkedin_url')->label('LinkedIn')->url()->maxLength(500),
                    ])->columns(2),
            ]);
    }
}
