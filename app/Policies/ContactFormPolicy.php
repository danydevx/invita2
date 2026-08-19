<?php

namespace App\Policies;

use App\Models\User;
use Modules\Businesses\Models\Business;

class ContactFormPolicy
{
    public function submit(User $user, Business $business): bool
    {
        if ($business->is_published && $business->is_active) {
            return true;
        }

        if ($user && $user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        if ($user && $user->id === $business->user_id) {
            return true;
        }

        return false;
    }
}
