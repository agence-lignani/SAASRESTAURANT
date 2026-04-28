<?php

namespace App\Policies;

use App\Models\MenuPhotoImport;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class MenuPhotoImportPolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function view(User $user, MenuPhotoImport $menuPhotoImport): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function update(User $user, MenuPhotoImport $menuPhotoImport): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function delete(User $user, MenuPhotoImport $menuPhotoImport): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }
}
