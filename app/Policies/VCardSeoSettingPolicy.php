<?php

namespace App\Policies;

use App\Models\User;
use Modules\VCards\Models\VCard;
use Modules\VCards\Models\VCardSeoSetting;

class VCardSeoSettingPolicy
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

    public function update(User $user, VCardSeoSetting $seoSetting): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $seoSetting->vcard->listing->user_id;
    }
}
