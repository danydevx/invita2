<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingProducts\Models\ListingProductCategory;
use Modules\Listings\Models\Listing;

class ListingProductCategoryPolicy
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

    public function update(User $user, ListingProductCategory $category): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $category->listing->user_id;
    }

    public function delete(User $user, ListingProductCategory $category): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $category->listing->user_id;
    }
}
