<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Appointments\Enums\AppointmentStatus;

class BusinessAppointment extends Model
{
    protected $fillable = [
        'business_id',
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\Locations\Models\BusinessLocation::class, 'business_location_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(\Modules\Services\Models\BusinessService::class, 'business_service_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
