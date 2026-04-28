<?php

namespace App\Policies;

use App\Models\SitePost;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class SitePostPolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function view(User $user, SitePost $sitePost): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function update(User $user, SitePost $sitePost): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function delete(User $user, SitePost $sitePost): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }
}
