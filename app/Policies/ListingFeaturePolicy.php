<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingFeatures\Models\ListingFeature;
use Modules\Listings\Models\Listing;

class ListingFeaturePolicy
{
    public function viewAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function view(User $user, ListingFeature $feature): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $feature->listing?->user_id === $user->id;
    }

    public function create(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function update(User $user, ListingFeature $feature): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $feature->listing?->user_id === $user->id;
    }

    public function delete(User $user, ListingFeature $feature): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $feature->listing?->user_id === $user->id;
    }
}
