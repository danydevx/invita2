<?php

namespace App\Policies;

use App\Models\User;
use Modules\Appointments\Models\BusinessAppointmentSlot;
use Modules\Listings\Models\Listing;

class BusinessAppointmentSlotPolicy
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

    public function update(User $user, BusinessAppointmentSlot $slot): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $slot->business->user_id;
    }

    public function delete(User $user, BusinessAppointmentSlot $slot): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $slot->business->user_id;
    }
}
