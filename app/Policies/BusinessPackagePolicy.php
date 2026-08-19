<?php

namespace App\Policies;

use App\Models\User;
use Modules\Businesses\Models\Business;
use Modules\Packages\Models\BusinessPackage;

class BusinessPackagePolicy
{
    public function viewAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function create(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function update(User $user, BusinessPackage $package): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $package->business->user_id;
    }

    public function delete(User $user, BusinessPackage $package): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $package->business->user_id;
    }

    public function deleteAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }
}
