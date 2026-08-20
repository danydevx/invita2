<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingAbout\Models\ListingAbout;
use Modules\Listings\Models\Listing;

class ListingAboutPolicy
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

    public function update(User $user, ListingAbout $about = null, Listing $business = null): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if ($about) {
            return $user->id === $about->listing->user_id;
        }

        if ($business) {
            return $user->id === $business->user_id;
        }

        return false;
    }

    public function delete(User $user, ListingAbout $about): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $about->listing->user_id;
    }
}
