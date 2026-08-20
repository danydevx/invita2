<?php

namespace App\Policies;

use App\Models\User;
use Modules\Listings\Models\Listing;
use Modules\ListingPackages\Models\ListingPackage;

class ListingPackagePolicy
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

    public function update(User $user, ListingPackage $package): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $package->listing?->user_id;
    }

    public function delete(User $user, ListingPackage $package): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $package->listing?->user_id;
    }

    public function deleteAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }
}
