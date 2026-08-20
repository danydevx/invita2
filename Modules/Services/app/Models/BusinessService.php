<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessService extends Model
{

    protected $table = 'listing_services';

    protected $fillable = [
        'listing_id',
        'business_location_id',
        'name',
        'slug',
        'description',
        'image',
        'duration_minutes',
        'price',
        'deposit_amount',
        'deposit_required',
        'allows_online_booking',
        'whatsapp_contact',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'duration_minutes' => 'integer',
        'price' => 'decimal:2',
        'deposit_amount' => 'decimal:2',
        'deposit_required' => 'boolean',
        'allows_online_booking' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\Locations\Models\BusinessLocation::class, 'business_location_id');
    }

    public function appointmentSlots(): HasMany
    {
        return $this->hasMany(\Modules\Appointments\Models\BusinessAppointmentSlot::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(BusinessServiceImage::class);
    }
}
