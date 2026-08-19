<?php

namespace App\Policies;

use App\Models\Subscription;
use App\Models\User;

class SubscriptionPolicy
{
    public function view(User $user, Subscription $subscription): bool
    {
        // Permite ver suscripciones propias o con permiso administrativo.
        return $subscription->user_id === $user->id || $user->can('subscriptions.view');
    }

    public function update(User $user, Subscription $subscription): bool
    {
        // Permite actualizar suscripciones solo con permiso administrativo.
        return $user->can('subscriptions.update');
    }

    public function cancel(User $user, Subscription $subscription): bool
    {
        // Permite cancelar suscripciones solo con permiso administrativo.
        return $user->can('subscriptions.cancel');
    }
}
