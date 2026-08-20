<?php

namespace Modules\ListingAppointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\ListingAppointments\Enums\AppointmentStatus;

class ListingAppointment extends Model
{

    protected $table = 'listing_appointments';

    protected $fillable = [
        'listing_id',
        'business_location_id',
        'business_service_id',
        'user_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'appointment_date',
        'start_time',
        'end_time',
        'status',
        'notes',
        'confirmation_token',
        'cancelled_at',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'status' => AppointmentStatus::class,
        'cancelled_at' => 'datetime',
    ];

    protected $hidden = [
        'confirmation_token',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingLocations\Models\ListingLocation::class, 'business_location_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingServices\Models\ListingService::class, 'business_service_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
