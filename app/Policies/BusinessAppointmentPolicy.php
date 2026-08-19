<?php

namespace App\Policies;

use App\Models\User;
use Modules\Appointments\Models\BusinessAppointment;
use Modules\Businesses\Models\Business;

class BusinessAppointmentPolicy
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
        if ($business->is_published && $business->is_active) {
            return true;
        }

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function update(User $user, BusinessAppointment $appointment): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if ($user->id === $appointment->business->user_id) {
            return true;
        }

        return $user->id === $appointment->customer_id;
    }

    public function cancel(User $user, BusinessAppointment $appointment): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if ($user->id === $appointment->business->user_id) {
            return true;
        }

        return $user->id === $appointment->customer_id;
    }

    public function delete(User $user, BusinessAppointment $appointment): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $appointment->business->user_id;
    }

    public function deleteAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }
}
