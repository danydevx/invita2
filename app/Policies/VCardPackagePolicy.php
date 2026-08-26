<?php

namespace App\Policies;

use App\Models\User;
use Modules\VCards\Models\VCard;
use Modules\VCards\Models\VCardPackage;

class VCardPackagePolicy
{
    public function viewAny(User $user, VCard $vcard): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $vcard->listing->user_id;
    }

    public function create(User $user, VCard $vcard): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $vcard->listing->user_id;
    }

    public function update(User $user, VCardPackage $package): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $package->vcard->listing->user_id;
    }

    public function delete(User $user, VCardPackage $package): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $package->vcard->listing->user_id;
    }
}
