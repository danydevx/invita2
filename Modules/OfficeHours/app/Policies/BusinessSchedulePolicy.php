<?php

namespace Modules\OfficeHours\Policies;

use App\Models\User;
use Modules\Businesses\Models\Business;
use Modules\OfficeHours\Models\BusinessSchedule;

class BusinessSchedulePolicy
{
    public function viewAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function view(User $user, BusinessSchedule $schedule): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $schedule->business?->user_id === $user->id;
    }

    public function create(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function update(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $business->user_id === $user->id;
    }

    public function delete(User $user, BusinessSchedule $schedule): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $schedule->business?->user_id === $user->id;
    }
}
