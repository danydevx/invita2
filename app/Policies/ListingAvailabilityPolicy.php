<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingAppointments\Models\ListingAvailability;
use Modules\Listings\Models\Listing;

class ListingAvailabilityPolicy
{
    public function viewAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function view(User $user, ListingAvailability $availability): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $availability->listing?->user_id === $user->id;
    }

    public function create(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function update(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function delete(User $user, ListingAvailability $availability): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $availability->listing?->user_id === $user->id;
    }
}
