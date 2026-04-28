<?php

namespace App\Policies;

use App\Models\BookingService;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class BookingServicePolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function view(User $user, BookingService $bookingService): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function update(User $user, BookingService $bookingService): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function delete(User $user, BookingService $bookingService): bool
    {
        return FilamentAccess::canManageBookings();
    }
}
