<?php

namespace Modules\ListingLocations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingLocation extends Model
{

    protected $table = 'listing_locations';

    protected $fillable = [
        'listing_id',
        'name',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'state_code',
        'municipality',
        'postal_code',
        'country',
        'phone',
        'email',
        'latitude',
        'longitude',
        'directions_url',
        'image',
        'is_primary',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(\Modules\ListingGallery\Models\ListingGalleryImage::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(\Modules\ListingProducts\Models\ListingProduct::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(\Modules\ListingServices\Models\ListingService::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(\Modules\ListingLeads\Models\ListingLead::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(\Modules\ListingAppointments\Models\ListingAppointment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(\Modules\ListingOfficeHours\Models\ListingSchedule::class, 'business_location_id');
    }
}
