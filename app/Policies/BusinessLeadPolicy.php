<?php

namespace App\Policies;

use App\Models\User;
use Modules\Leads\Models\BusinessLead;
use Modules\Businesses\Models\Business;

class BusinessLeadPolicy
{
    public function viewAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function view(User $user, BusinessLead $lead): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $lead->business->user_id;
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

    public function update(User $user, BusinessLead $lead): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $lead->business->user_id;
    }

    public function delete(User $user, BusinessLead $lead): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $lead->business->user_id;
    }

    public function deleteAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }
}
