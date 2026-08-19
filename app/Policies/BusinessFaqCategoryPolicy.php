<?php

namespace App\Policies;

use App\Models\User;
use Modules\Faqs\Models\BusinessFaqCategory;
use Modules\Businesses\Models\Business;

class BusinessFaqCategoryPolicy
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

    public function update(User $user, BusinessFaqCategory $category): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $category->business->user_id;
    }

    public function delete(User $user, BusinessFaqCategory $category): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $category->business->user_id;
    }
}
