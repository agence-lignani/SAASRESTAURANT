<?php

namespace App\Policies;

use App\Models\BookingSetting;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class BookingSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function view(User $user, BookingSetting $bookingSetting): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function update(User $user, BookingSetting $bookingSetting): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function delete(User $user, BookingSetting $bookingSetting): bool
    {
        return false;
    }
}
