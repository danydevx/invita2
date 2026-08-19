<?php

namespace App\Policies;

use App\Models\User;
use Modules\About\Models\BusinessAbout;
use Modules\Businesses\Models\Business;

class BusinessAboutPolicy
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

    public function update(User $user, BusinessAbout $about = null, Business $business = null): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if ($about) {
            return $user->id === $about->business->user_id;
        }

        if ($business) {
            return $user->id === $business->user_id;
        }

        return false;
    }

    public function delete(User $user, BusinessAbout $about): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $about->business->user_id;
    }
}
