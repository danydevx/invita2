<?php

namespace Modules\ListingMinisite\Policies;

use App\Models\User;
use Modules\Listings\Models\Listing;
use Modules\ListingMinisite\Models\ListingMinisiteSection;
use Modules\ListingMinisite\Models\ListingMinisiteSetting;

class ListingMinisitePolicy
{
    public function viewAny(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $listing->user_id;
    }

    public function view(User $user, ListingMinisiteSetting $setting): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $setting->listing->user_id;
    }

    public function create(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $listing->user_id;
    }

    public function update(User $user, ListingMinisiteSetting $setting): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $setting->listing->user_id;
    }

    public function delete(User $user, ListingMinisiteSetting $setting): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $setting->listing->user_id;
    }

    public function manageSections(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $listing->user_id;
    }

    public function manageSection(User $user, ListingMinisiteSection $section): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $section->listing->user_id;
    }
}