<?php

namespace App\Filament\Resources\BookingServices;

use App\Filament\Concerns\BelongsToCurrentRestaurant;
use App\Filament\Resources\BookingServices\Pages\CreateBookingService;
use App\Filament\Resources\BookingServices\Pages\EditBookingService;
use App\Filament\Resources\BookingServices\Pages\ListBookingServices;
use App\Filament\Resources\BookingServices\Schemas\BookingServiceForm;
use App\Filament\Resources\BookingServices\Tables\BookingServicesTable;
use App\Models\BookingService;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BookingServiceResource extends Resource
{
    use BelongsToCurrentRestaurant;

    protected static ?string $model = BookingService::class;

    protected static ?string $navigationLabel = 'Services';

    protected static ?string $modelLabel = 'service';

    protected static ?string $pluralModelLabel = 'services réservation';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendar;

    protected static string|UnitEnum|null $navigationGroup = 'Réservations';

    protected static ?int $navigationSort = 60;

    public static function form(Schema $schema): Schema
    {
        return BookingServiceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BookingServicesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBookingServices::route('/'),
            'create' => CreateBookingService::route('/create'),
            'edit' => EditBookingService::route('/{record}/edit'),
        ];
    }
}
