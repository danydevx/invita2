<?php

namespace Modules\ListingAppointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingAppointmentSlot extends Model
{

    protected $table = 'listing_appointment_slots';

    protected $fillable = [
        'listing_id',
        'business_service_id',
        'business_location_id',
        'day_of_week',
        'specific_date',
        'start_time',
        'end_time',
        'is_available',
        'slots_available',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'specific_date' => 'date',
        'is_available' => 'boolean',
        'slots_available' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingServices\Models\ListingService::class, 'business_service_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingLocations\Models\ListingLocation::class, 'business_location_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('slots_available', '>', 0);
    }
}
