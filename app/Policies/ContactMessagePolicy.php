<?php

namespace App\Policies;

use App\Models\ContactMessage;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class ContactMessagePolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::isOwner()
            || FilamentAccess::isReservation()
            || FilamentAccess::isEditor();
    }

    public function view(User $user, ContactMessage $contactMessage): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, ContactMessage $contactMessage): bool
    {
        return FilamentAccess::isOwner() || FilamentAccess::isReservation();
    }

    public function delete(User $user, ContactMessage $contactMessage): bool
    {
        return FilamentAccess::isOwner();
    }
}
