<?php

namespace App\Policies;

use App\Models\User;
use Modules\Appointments\Models\BusinessAvailability;
use Modules\Businesses\Models\Business;

class BusinessAvailabilityPolicy
{
    public function viewAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function view(User $user, BusinessAvailability $availability): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $availability->business?->user_id === $user->id;
    }

    public function create(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function update(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function delete(User $user, BusinessAvailability $availability): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $availability->business?->user_id === $user->id;
    }
}