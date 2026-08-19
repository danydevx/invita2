<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessAppointmentSlot extends Model
{
    protected $fillable = [
        'business_id',
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(\Modules\Services\Models\BusinessService::class, 'business_service_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\Locations\Models\BusinessLocation::class, 'business_location_id');
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true)->where('slots_available', '>', 0);
    }
}
