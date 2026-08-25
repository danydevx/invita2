<?php

namespace App\Policies;

use App\Models\User;
use Modules\Properties\Models\Property;
use Modules\Listings\Models\Listing;

class PropertyPolicy
{
    public function viewAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function view(User $user, Property $property): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $property->listing && $user->id === $property->listing->user_id;
    }

    public function create(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $listing->user_id;
    }

    public function update(User $user, Property $property): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $property->listing && $user->id === $property->listing->user_id;
    }

    public function delete(User $user, Property $property): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $property->listing && $user->id === $property->listing->user_id;
    }

    public function deleteAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }
}
