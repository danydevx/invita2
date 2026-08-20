<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingAppointments\Models\ListingAppointment;
use Modules\Listings\Models\Listing;

class ListingAppointmentPolicy
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
        if ($business->is_published && $business->is_active) {
            return true;
        }

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function update(User $user, ListingAppointment $appointment): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if ($user->id === $appointment->listing->user_id) {
            return true;
        }

        return $user->id === $appointment->customer_id;
    }

    public function cancel(User $user, ListingAppointment $appointment): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if ($user->id === $appointment->listing->user_id) {
            return true;
        }

        return $user->id === $appointment->customer_id;
    }

    public function delete(User $user, ListingAppointment $appointment): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $appointment->listing->user_id;
    }

    public function deleteAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }
}
