<?php

namespace Modules\ClientFidelity\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Modules\ClientFidelity\Models\ClientFidelityCard;
use Modules\Listings\Models\Listing;

class ClientFidelityCardPolicy
{
    use HandlesAuthorization;

    public function viewAny(Listing $listing): bool
    {
        return $listing->user_id === auth()->id();
    }

    public function view(Listing $listing, ClientFidelityCard $card): bool
    {
        return $card->listing_id === $listing->id && $listing->user_id === auth()->id();
    }

    public function create(Listing $listing): bool
    {
        return $listing->user_id === auth()->id();
    }

    public function update(Listing $listing, ClientFidelityCard $card): bool
    {
        return $card->listing_id === $listing->id && $listing->user_id === auth()->id();
    }

    public function delete(Listing $listing, ClientFidelityCard $card): bool
    {
        return $card->listing_id === $listing->id && $listing->user_id === auth()->id();
    }

    public function scan(Listing $listing, ClientFidelityCard $card): bool
    {
        return $card->listing_id === $listing->id && $listing->user_id === auth()->id();
    }

    public function reset(Listing $listing, ClientFidelityCard $card): bool
    {
        return $card->listing_id === $listing->id && $listing->user_id === auth()->id();
    }
}
