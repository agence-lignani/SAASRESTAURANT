<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserInvitation;
use App\Support\Filament\FilamentAccess;

class UserInvitationPolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canInviteTeam();
    }

    public function view(User $user, UserInvitation $userInvitation): bool
    {
        return FilamentAccess::canInviteTeam();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canInviteTeam();
    }

    public function update(User $user, UserInvitation $userInvitation): bool
    {
        return false;
    }

    public function delete(User $user, UserInvitation $userInvitation): bool
    {
        return FilamentAccess::canInviteTeam();
    }
}
