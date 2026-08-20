<?php

namespace Modules\ListingGuests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingGuest extends Model
{
    protected $table = 'listing_guests';

    protected $fillable = [
        'listing_id',
        'name',
        'email',
        'phone',
        'rsvp_status',
        'plus_ones',
        'notes',
        'confirmation_token',
        'sent_at',
        'confirmed_at',
        'declined_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'declined_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}