<?php

namespace Modules\Features\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Features\Models\FeatureCategory;

class FeatureCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny($user): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function view($user, FeatureCategory $category): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function create($user): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function update($user, FeatureCategory $category): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function delete($user, FeatureCategory $category): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }
}
