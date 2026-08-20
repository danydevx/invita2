<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingAppointments\Models\ListingAppointmentSlot;
use Modules\Listings\Models\Listing;

class ListingAppointmentSlotPolicy
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

    public function update(User $user, ListingAppointmentSlot $slot): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $slot->listing->user_id;
    }

    public function delete(User $user, ListingAppointmentSlot $slot): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $slot->listing->user_id;
    }
}
