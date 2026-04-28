<?php

namespace App\Policies;

use App\Models\MenuCategory;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class MenuCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function view(User $user, MenuCategory $menuCategory): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function update(User $user, MenuCategory $menuCategory): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function delete(User $user, MenuCategory $menuCategory): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }
}
