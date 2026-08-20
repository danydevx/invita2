<?php

namespace App\Policies;

use App\Models\User;
use Modules\ListingModules\Models\ListingModule;
use Modules\Listings\Models\Listing;

class ListingModulePolicy
{
    public function viewAny(User $user, Listing $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function update(User $user, ListingModule $module): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $module->listing->user_id;
    }
}
