<?php

namespace App\Policies;

use App\Models\User;
use Modules\Listings\Models\Listing;
use Modules\ListingTeamMembers\Models\ListingTeamMember;
use Modules\ListingTeamMembers\Models\TeamMemberPosition;

class ListingTeamMemberPolicy
{
    public function viewAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function create(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function update(User $user, ListingTeamMember $member): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $member->business->user_id;
    }

    public function delete(User $user, ListingTeamMember $member): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $member->business->user_id;
    }

    public function updatePosition(User $user, TeamMemberPosition $position): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $position->business->user_id;
    }

    public function deletePosition(User $user, TeamMemberPosition $position): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $position->business->user_id;
    }

    public function deleteAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }
}
