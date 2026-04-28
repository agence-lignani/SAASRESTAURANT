<?php

namespace App\Policies;

use App\Models\GalleryMedia;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class GalleryMediaPolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function view(User $user, GalleryMedia $galleryMedia): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function update(User $user, GalleryMedia $galleryMedia): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }

    public function delete(User $user, GalleryMedia $galleryMedia): bool
    {
        return FilamentAccess::canEditSiteAndMenu();
    }
}
