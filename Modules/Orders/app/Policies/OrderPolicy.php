<?php

namespace Modules\Orders\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Businesses\Models\Business;
use Modules\Orders\Models\Order;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(Business $business, Order $order): bool
    {
        return $business->user_id === auth()->id();
    }

    public function view(Business $business, Order $order): bool
    {
        return $order->business_id === $business->id && $business->user_id === auth()->id();
    }

    public function create(Business $business): bool
    {
        return $business->user_id === auth()->id();
    }

    public function update(Business $business, Order $order): bool
    {
        return $order->business_id === $business->id && $business->user_id === auth()->id();
    }

    public function delete(Business $business, Order $order): bool
    {
        return $order->business_id === $business->id && $business->user_id === auth()->id();
    }

    public function updateStatus(Business $business, Order $order): bool
    {
        return $order->business_id === $business->id && $business->user_id === auth()->id();
    }
}
