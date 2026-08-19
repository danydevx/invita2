<?php

namespace App\Policies;

use App\Models\User;
use Modules\Products\Models\BusinessProductCategory;
use Modules\Businesses\Models\Business;

class BusinessProductCategoryPolicy
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

    public function update(User $user, BusinessProductCategory $category): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $category->business->user_id;
    }

    public function delete(User $user, BusinessProductCategory $category): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $category->business->user_id;
    }
}
