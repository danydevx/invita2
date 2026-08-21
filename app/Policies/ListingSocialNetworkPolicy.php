<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingSocialMedia\Models\ListingSocialNetwork;
use Modules\Listings\Models\Listing;

class ListingSocialNetworkPolicy
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

    public function update(User $user, ListingSocialNetwork $socialNetwork): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $socialNetwork->business && $user->id === $socialNetwork->business->user_id;
    }

    public function delete(User $user, ListingSocialNetwork $socialNetwork): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $socialNetwork->business && $user->id === $socialNetwork->business->user_id;
    }
}
