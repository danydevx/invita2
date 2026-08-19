<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WebhookEndpoint;

class WebhookEndpointPolicy
{
    public function view(User $user, WebhookEndpoint $endpoint): bool
    {
        // Permite ver el webhook si es propio o si tiene permiso administrativo.
        return $endpoint->user_id === $user->id || $user->can('webhooks.view');
    }

    public function update(User $user, WebhookEndpoint $endpoint): bool
    {
        // Permite actualizar si es propio o si tiene permiso administrativo.
        if ($endpoint->user_id === $user->id) {
            return $user->can('webhooks.manage');
        }

        return $user->can('webhooks.update');
    }

    public function delete(User $user, WebhookEndpoint $endpoint): bool
    {
        // Permite eliminar si es propio o si tiene permiso administrativo.
        if ($endpoint->user_id === $user->id) {
            return $user->can('webhooks.manage');
        }

        return $user->can('webhooks.update');
    }
}
