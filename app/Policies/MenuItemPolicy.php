<?php

namespace App\Policies;

use App\Models\MenuItem;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class MenuItemPolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function view(User $user, MenuItem $menuItem): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function update(User $user, MenuItem $menuItem): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function delete(User $user, MenuItem $menuItem): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }
}
