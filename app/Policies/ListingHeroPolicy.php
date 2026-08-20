<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingHero\Models\ListingHero;
use Modules\Listings\Models\Listing;

class ListingHeroPolicy
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

    public function update(User $user, ListingHero $hero = null, Listing $business = null): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if ($hero) {
            return $user->id === $hero->listing->user_id;
        }

        if ($business) {
            return $user->id === $business->user_id;
        }

        return false;
    }

    public function delete(User $user, ListingHero $hero): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $hero->listing->user_id;
    }
}
