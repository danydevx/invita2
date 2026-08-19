<?php

namespace Modules\Appointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessAvailabilityException extends Model
{
    protected $table = 'business_availability_exceptions';

    protected $fillable = [
        'business_id',
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }
}