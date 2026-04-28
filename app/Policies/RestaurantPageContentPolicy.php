<?php

namespace App\Policies;

use App\Models\RestaurantPageContent;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class RestaurantPageContentPolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function view(User $user, RestaurantPageContent $restaurantPageContent): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function update(User $user, RestaurantPageContent $restaurantPageContent): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function delete(User $user, RestaurantPageContent $restaurantPageContent): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }
}
