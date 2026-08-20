<?php

namespace Modules\Checkin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessCheckin extends Model
{
    protected $table = 'listing_checkins';

    protected $fillable = [
        'listing_id',
        'guest_id',
        'checkin_time',
        'plus_ones_checked_in',
        'notes',
    ];

    protected $casts = [
        'checkin_time' => 'datetime',
        'plus_ones_checked_in' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function guest(): BelongsTo
    {
        return $this->belongsTo(\Modules\Guests\Models\BusinessGuest::class);
    }
}
