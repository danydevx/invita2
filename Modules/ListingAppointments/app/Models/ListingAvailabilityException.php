<?php

namespace Modules\ListingAppointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingAvailabilityException extends Model
{
    protected $table = 'listing_availability_exceptions';

    protected $fillable = [
        'listing_id',
        'exception_date',
        'is_available',
        'start_time',
        'end_time',
        'reason',
        'slots_per_slot',
    ];

    protected $casts = [
        'exception_date' => 'date',
        'is_available' => 'boolean',
        'start_time' => 'string',
        'end_time' => 'string',
        'slots_per_slot' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }
}
