<?php

namespace App\Policies;

use App\Models\User;
use Modules\SocialMedia\Models\BusinessSocialNetwork;
use Modules\Businesses\Models\Business;

class BusinessSocialNetworkPolicy
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

    public function update(User $user, BusinessSocialNetwork $socialNetwork): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $socialNetwork->business->user_id;
    }

    public function delete(User $user, BusinessSocialNetwork $socialNetwork): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $socialNetwork->business->user_id;
    }
}
