<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingLeads\Models\ListingLead;
use Modules\Listings\Models\Listing;

class ListingLeadPolicy
{
    public function viewAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function view(User $user, ListingLead $lead): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $lead->listing->user_id;
    }

    public function create(User $user, Listing $business): bool
    {
        if ($business->is_published && $business->is_active) {
            return true;
        }

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function update(User $user, ListingLead $lead): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $lead->listing->user_id;
    }

    public function delete(User $user, ListingLead $lead): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $lead->listing->user_id;
    }

    public function deleteAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }
}
