<?php

namespace Modules\Minisite\Policies;

use App\Models\User;
use Modules\Businesses\Models\Business;
use Modules\Minisite\Models\BusinessMinisiteSection;
use Modules\Minisite\Models\BusinessMinisiteSetting;

class MinisitePolicy
{
    public function viewAny(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function view(User $user, BusinessMinisiteSetting $setting): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $setting->business->user_id;
    }

    public function create(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function update(User $user, BusinessMinisiteSetting $setting): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $setting->business->user_id;
    }

    public function delete(User $user, BusinessMinisiteSetting $setting): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $setting->business->user_id;
    }

    public function manageSections(User $user, Business $business): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $business->user_id;
    }

    public function manageSection(User $user, BusinessMinisiteSection $section): bool
    {
        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            return true;
        }

        return $user->id === $section->business->user_id;
    }
}
