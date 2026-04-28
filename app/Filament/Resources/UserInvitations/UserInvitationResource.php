<?php

namespace App\Filament\Resources\UserInvitations;

use App\Filament\Concerns\BelongsToCurrentRestaurant;
use App\Filament\Resources\UserInvitations\Pages\CreateUserInvitation;
use App\Filament\Resources\UserInvitations\Pages\ListUserInvitations;
use App\Filament\Resources\UserInvitations\Schemas\UserInvitationForm;
use App\Filament\Resources\UserInvitations\Tables\UserInvitationsTable;
use App\Models\UserInvitation;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class UserInvitationResource extends Resource
{
    use BelongsToCurrentRestaurant;

    protected static ?string $model = UserInvitation::class;

    protected static ?string $navigationLabel = 'Invitations';

    protected static ?string $modelLabel = 'invitation';

    protected static ?string $pluralModelLabel = 'invitations';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Équipe';

    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return UserInvitationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UserInvitationsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUserInvitations::route('/'),
            'create' => CreateUserInvitation::route('/create'),
        ];
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }
}
