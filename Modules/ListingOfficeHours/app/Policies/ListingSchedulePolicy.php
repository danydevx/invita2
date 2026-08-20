<?php

namespace Modules\ListingOfficeHours\Policies;

use App\Models\User;
use Modules\Listings\Models\Listing;
use Modules\ListingOfficeHours\Models\ListingSchedule;

class ListingSchedulePolicy
{
    public function viewAny(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $listing->user_id === $user->id;
    }

    public function view(User $user, ListingSchedule $schedule): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $schedule->listing?->user_id === $user->id;
    }

    public function create(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $listing->user_id === $user->id;
    }

    public function update(User $user, Listing $listing): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $listing->user_id === $user->id;
    }

    public function delete(User $user, ListingSchedule $schedule): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $schedule->listing?->user_id === $user->id;
    }
}