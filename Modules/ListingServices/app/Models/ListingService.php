<?php

namespace Modules\ListingServices\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingService extends Model
{

    protected $table = 'listing_services';

    protected $fillable = [
        'listing_id',
        'category_id',
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

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ListingServiceCategory::class, 'category_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\ListingLocations\Models\ListingLocation::class, 'business_location_id');
    }

    public function appointmentSlots(): HasMany
    {
        return $this->hasMany(\Modules\ListingAppointments\Models\ListingAppointmentSlot::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ListingServiceImage::class);
    }
}
