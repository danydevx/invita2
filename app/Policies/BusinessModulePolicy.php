<?php

namespace App\Policies;

use App\Models\User;
use Modules\BusinessModules\Models\BusinessModule;
use Modules\Businesses\Models\Business;

class BusinessModulePolicy
{
    public function viewAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function update(User $user, BusinessModule $module): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $module->business->user_id;
    }
}
