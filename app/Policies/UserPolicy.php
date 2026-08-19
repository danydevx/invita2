<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function view(User $user, User $target): bool
    {
        // Permite ver usuarios solo con permiso administrativo.
        return $user->can('users.view');
    }

    public function update(User $user, User $target): bool
    {
        // Permite editar usuarios solo con permiso administrativo.
        return $user->can('users.update');
    }

    public function assignRoles(User $user, User $target): bool
    {
        // Permite asignar roles solo con permiso administrativo.
        return $user->can('users.assign_roles');
    }
}
