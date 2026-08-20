<?php

namespace Modules\ListingFeatures\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\ListingFeatures\Models\Feature;
use Modules\Listings\Models\Listing;

class FeaturePolicy
{
    use HandlesAuthorization;

    public function viewAnyAdmin($user): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function viewAdmin($user, Feature $feature): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function createAdmin($user): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function updateAdmin($user, Feature $feature): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function deleteAdmin($user, Feature $feature): bool
    {
        if ($feature->isPredefined()) {
            return $user->hasRole('superadmin');
        }

        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function viewAnyMember($user, Listing $business): bool
    {
        return $business->user_id === $user->id;
    }

    public function viewMember($user, Feature $feature, Listing $business): bool
    {
        return $feature->listing_id === $business->id || $feature->isPredefined();
    }

    public function createMember($user, Listing $business): bool
    {
        return $business->user_id === $user->id;
    }

    public function updateMember($user, Feature $feature, Listing $business): bool
    {
        if ($feature->listing_id !== $business->id) {
            return false;
        }

        if ($feature->isPredefined() || $feature->isClone()) {
            return false;
        }

        return $business->user_id === $user->id;
    }

    public function deleteMember($user, Feature $feature, Listing $business): bool
    {
        if ($feature->listing_id !== $business->id) {
            return false;
        }

        if ($feature->isPredefined()) {
            return false;
        }

        return $business->user_id === $user->id;
    }

    public function import($user, Listing $business): bool
    {
        return $business->user_id === $user->id;
    }
}
