<?php

namespace App\Filament\Resources\BookingSettings;

use App\Filament\Concerns\BelongsToCurrentRestaurant;
use App\Filament\Resources\BookingSettings\Pages\CreateBookingSetting;
use App\Filament\Resources\BookingSettings\Pages\EditBookingSetting;
use App\Filament\Resources\BookingSettings\Pages\ListBookingSettings;
use App\Filament\Resources\BookingSettings\Schemas\BookingSettingForm;
use App\Filament\Resources\BookingSettings\Tables\BookingSettingsTable;
use App\Models\BookingSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

class BookingSettingResource extends Resource
{
    use BelongsToCurrentRestaurant;

    protected static ?string $model = BookingSetting::class;

    protected static ?string $navigationLabel = 'Paramètres';

    protected static ?string $modelLabel = 'paramètre réservation';

    protected static ?string $pluralModelLabel = 'paramètres réservation';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static string|UnitEnum|null $navigationGroup = 'Réservations';

    protected static ?int $navigationSort = 61;

    public static function form(Schema $schema): Schema
    {
        return BookingSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingSettingsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingSettings::route('/'),
            'create' => CreateBookingSetting::route('/create'),
            'edit' => EditBookingSetting::route('/{record}/edit'),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }
}
