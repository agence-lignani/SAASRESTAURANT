<?php

namespace App\Policies;

use App\Models\LegalPage;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class LegalPagePolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function view(User $user, LegalPage $legalPage): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function update(User $user, LegalPage $legalPage): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function delete(User $user, LegalPage $legalPage): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }
}
