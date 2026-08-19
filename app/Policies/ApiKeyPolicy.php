<?php

namespace App\Policies;

use App\Models\ApiKey;
use App\Models\User;

class ApiKeyPolicy
{
    public function view(User $user, ApiKey $apiKey): bool
    {
        // Permite ver la API key si es propia o si tiene permiso administrativo.
        return $apiKey->user_id === $user->id || $user->can('api-keys.view');
    }

    public function update(User $user, ApiKey $apiKey): bool
    {
        // Permite actualizar si es propia o si tiene permiso administrativo.
        if ($apiKey->user_id === $user->id) {
            return $user->can('api-keys.manage');
        }

        return $user->can('api-keys.update');
    }

    public function delete(User $user, ApiKey $apiKey): bool
    {
        // Permite revocar si es propia o si tiene permiso administrativo.
        if ($apiKey->user_id === $user->id) {
            return $user->can('api-keys.manage');
        }

        return $user->can('api-keys.revoke');
    }
}
