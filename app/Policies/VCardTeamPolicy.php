<?php

namespace App\Policies;

use App\Models\User;
use Modules\Listings\Models\Listing;
use Modules\VCards\Models\VCardTeam;

class VCardTeamPolicy
{
    public function viewAny(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $listing->user_id;
    }

    public function create(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $listing->user_id;
    }

    public function view(User $user, VCardTeam $team): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $team->listing->user_id;
    }

    public function update(User $user, VCardTeam $team): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $team->listing->user_id;
    }

    public function delete(User $user, VCardTeam $team): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $team->listing->user_id;
    }

    public function deleteAny(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $listing->user_id;
    }
}
