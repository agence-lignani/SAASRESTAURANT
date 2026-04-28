<?php

namespace App\Filament\Pages;

use App\Models\RestaurantChatSetting;
use App\Support\Filament\FilamentAccess;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Panel;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;

class ManageChatAssistantPage extends RestaurantSettingsPage
{
    public static function canAccess(): bool
    {
        return FilamentAccess::isOwner();
    }

    protected static ?string $navigationLabel = 'Assistant IA (chat)';

    protected static ?int $navigationSort = 44;

    protected static ?string $title = 'Assistant conversationnel (F20)';

    public static function getSlug(?Panel $panel = null): string
    {
        return 'assistant-ia';
    }

    protected function getFormRecord(): Model
    {
        return RestaurantChatSetting::query()->firstOrCreate(
            ['restaurant_id' => $this->restaurant()->id],
            [
                'is_enabled' => false,
                'system_prompt_extra' => null,
                'max_user_message_length' => 2000,
                'max_messages_per_session' => 40,
                'max_messages_per_day_per_ip' => 80,
                'history_tail_messages' => 20,
                'widget_position' => 'bottom-end',
            ],
        );
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Activation site public')
                    ->description('Widget fixe : disclaimer affiché dans le panneau (§5.8). Les messages sont journalisés pour quotas et support.')
                    ->schema([
                        Toggle::make('is_enabled')
                            ->label('Activer l’assistant sur le site')
                            ->inline(false),
                        Select::make('widget_position')
                            ->label('Position du bouton')
                            ->options([
                                'bottom-end' => 'Bas à droite',
                                'bottom-start' => 'Bas à gauche',
                            ])
                            ->native(false),
                    ])->columns(1),
                Section::make('Consignes modèle')
                    ->schema([
                        Textarea::make('system_prompt_extra')
                            ->label('Consignes supplémentaires (optionnel)')
                            ->helperText('Ajoutées au prompt : ton, événements, informations sur les vins si saisies à la main dans la carte.')
                            ->rows(6),
                    ]),
                Section::make('Quotas & limites')
                    ->schema([
                        TextInput::make('max_user_message_length')
                            ->label('Taille max. d’un message (caractères)')
                            ->numeric()
                            ->minValue(256)
                            ->maxValue(8000)
                            ->required(),
                        TextInput::make('max_messages_per_session')
                            ->label('Messages visiteur max. / session')
                            ->numeric()
                            ->minValue(2)
                            ->maxValue(200)
                            ->required(),
                        TextInput::make('max_messages_per_day_per_ip')
                            ->label('Messages visiteur max. / jour / adresse IP')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(500)
                            ->required(),
                        TextInput::make('history_tail_messages')
                            ->label('Historique envoyé au modèle (derniers messages)')
                            ->numeric()
                            ->minValue(2)
                            ->maxValue(100)
                            ->required(),
                    ])->columns(2),
            ]);
    }
}
