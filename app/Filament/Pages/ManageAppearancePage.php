<?php

namespace App\Filament\Pages;

use App\Models\RestaurantThemeSetting;
use App\Support\Filament\FilamentAccess;
use App\Theme\BistroManifest;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ManageAppearancePage extends RestaurantSettingsPage
{
    public static function canAccess(): bool
    {
        return FilamentAccess::isOwner();
    }

    protected static ?string $navigationLabel = 'Apparence';

    protected static ?int $navigationSort = 20;

    protected static ?string $title = 'Apparence (tokens)';

    public static function getSlug(?Panel $panel = null): string
    {
        return 'apparence';
    }

    protected function getFormRecord(): Model
    {
        return RestaurantThemeSetting::query()->firstOrCreate(
            ['restaurant_id' => $this->restaurant()->id],
            []
        );
    }

    public function form(Schema $schema): Schema
    {
        $fonts = BistroManifest::fontOptions();

        return $schema
            ->components([
                Section::make('Couleurs')
                    ->description('Trois couleurs pour tout le site public. Les fonds de section sont calculés automatiquement (mélanges légers à partir de la primaire et de la secondaire).')
                    ->schema([
                        ColorPicker::make('color_primary')
                            ->label('Primaire')
                            ->helperText('Boutons principaux, liens, pastilles et accents interactifs.'),
                        ColorPicker::make('color_secondary')
                            ->label('Secondaire')
                            ->helperText('Titres, en-tête, bandeaux contrastés et structure visuelle.'),
                        ColorPicker::make('color_text')
                            ->label('Texte')
                            ->helperText('Corps de texte, sous-titres et paragraphes.'),
                    ])
                    ->columns(3),
                Section::make('Rayons (rem)')
                    ->schema([
                        TextInput::make('radius_sm')->label('Petit')->numeric()->step(0.05)->minValue(0)->maxValue(3),
                        TextInput::make('radius_md')->label('Moyen')->numeric()->step(0.05)->minValue(0)->maxValue(3),
                        TextInput::make('radius_lg')->label('Grand')->numeric()->step(0.05)->minValue(0)->maxValue(3),
                    ])->columns(3),
                Section::make('Typographie')
                    ->description('Polices via Google Fonts : licences SIL Open Font License ou Apache 2.0 — gratuites, modifiables, utilisables commercialement (voir fonts.google.com/attribution). Les polices « système » ne téléchargent aucun fichier.')
                    ->schema([
                        Select::make('font_heading_key')
                            ->label('Police titres')
                            ->options($fonts)
                            ->searchable()
                            ->placeholder('Défaut : Plus Jakarta Sans'),
                        Select::make('font_body_key')
                            ->label('Police corps')
                            ->options($fonts)
                            ->searchable()
                            ->placeholder('Défaut : Plus Jakarta Sans'),
                    ])->columns(2),
            ]);
    }
}
