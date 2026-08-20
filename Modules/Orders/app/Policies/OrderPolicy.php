<?php

namespace Modules\Orders\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\Listings\Models\Listing;
use Modules\Orders\Models\Order;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(Listing $listing, Order $order): bool
    {
        return $listing->user_id === auth()->id();
    }

    public function view(Listing $listing, Order $order): bool
    {
        return $order->listing_id === $listing->id && $listing->user_id === auth()->id();
    }

    public function create(Listing $listing): bool
    {
        return $listing->user_id === auth()->id();
    }

    public function update(Listing $listing, Order $order): bool
    {
        return $order->listing_id === $listing->id && $listing->user_id === auth()->id();
    }

    public function delete(Listing $listing, Order $order): bool
    {
        return $order->listing_id === $listing->id && $listing->user_id === auth()->id();
    }

    public function updateStatus(Listing $listing, Order $order): bool
    {
        return $order->listing_id === $listing->id && $listing->user_id === auth()->id();
    }
}
