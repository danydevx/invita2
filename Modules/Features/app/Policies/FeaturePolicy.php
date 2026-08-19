<?php

namespace Modules\Features\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Features\Models\Feature;
use Modules\Businesses\Models\Business;

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

    public function viewAnyMember($user, Business $business): bool
    {
        return $business->user_id === $user->id;
    }

    public function viewMember($user, Feature $feature, Business $business): bool
    {
        return $feature->business_id === $business->id || $feature->isPredefined();
    }

    public function createMember($user, Business $business): bool
    {
        return $business->user_id === $user->id;
    }

    public function updateMember($user, Feature $feature, Business $business): bool
    {
        if ($feature->business_id !== $business->id) {
            return false;
        }

        if ($feature->isPredefined() || $feature->isClone()) {
            return false;
        }

        return $business->user_id === $user->id;
    }

    public function deleteMember($user, Feature $feature, Business $business): bool
    {
        if ($feature->business_id !== $business->id) {
            return false;
        }

        if ($feature->isPredefined()) {
            return false;
        }

        return $business->user_id === $user->id;
    }

    public function import($user, Business $business): bool
    {
        return $business->user_id === $user->id;
    }
}
