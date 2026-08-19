<?php

namespace App\Policies;

use App\Models\User;
use Modules\Businesses\Models\Business;

class BusinessPolicy
{
    public function viewAny(User $user): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }
        return $user->hasRole('member');
    }

    public function view(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if (!$business->is_published || !$business->is_active) {
            return $user->hasRole('member') && $user->id === $business->user_id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasRole('member');
    }

    public function update(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function delete(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function manageModules(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }
}
