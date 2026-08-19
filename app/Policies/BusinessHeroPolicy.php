<?php

namespace App\Policies;

use App\Models\User;
use Modules\Hero\Models\BusinessHero;
use Modules\Businesses\Models\Business;

class BusinessHeroPolicy
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

    public function update(User $user, BusinessHero $hero = null, Business $business = null): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if ($hero) {
            return $user->id === $hero->business->user_id;
        }

        if ($business) {
            return $user->id === $business->user_id;
        }

        return false;
    }

    public function delete(User $user, BusinessHero $hero): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $hero->business->user_id;
    }
}
