<?php

namespace Modules\ClientFidelity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\ClientFidelity\Models\ClientFidelityCard;
use Modules\Businesses\Models\Business;

class ClientFidelityCardPolicy
{
    use HandlesAuthorization;

    public function viewAny(Business $business): bool
    {
        return $business->user_id === auth()->id();
    }

    public function view(Business $business, ClientFidelityCard $card): bool
    {
        return $card->business_id === $business->id && $business->user_id === auth()->id();
    }

    public function create(Business $business): bool
    {
        return $business->user_id === auth()->id();
    }

    public function update(Business $business, ClientFidelityCard $card): bool
    {
        return $card->business_id === $business->id && $business->user_id === auth()->id();
    }

    public function delete(Business $business, ClientFidelityCard $card): bool
    {
        return $card->business_id === $business->id && $business->user_id === auth()->id();
    }

    public function scan(Business $business, ClientFidelityCard $card): bool
    {
        return $card->business_id === $business->id && $business->user_id === auth()->id();
    }

    public function reset(Business $business, ClientFidelityCard $card): bool
    {
        return $card->business_id === $business->id && $business->user_id === auth()->id();
    }
}
