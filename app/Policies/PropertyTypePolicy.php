<?php

namespace App\Policies;

use App\Models\User;
use Modules\Properties\Models\PropertyType;

class PropertyTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function view(User $user, PropertyType $propertyType): bool
    {
        return $user->hasAnyRole(['superadmin', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['superadmin']);
    }

    public function update(User $user, PropertyType $propertyType): bool
    {
        return $user->hasAnyRole(['superadmin']);
    }

    public function delete(User $user, PropertyType $propertyType): bool
    {
        return $user->hasAnyRole(['superadmin']);
    }
}
