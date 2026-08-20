<?php

namespace Modules\Locations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessLocation extends Model
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function galleryImages(): HasMany
    {
        return $this->hasMany(\Modules\Gallery\Models\BusinessGalleryImage::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(\Modules\Products\Models\BusinessProduct::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(\Modules\Services\Models\BusinessService::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(\Modules\Leads\Models\BusinessLead::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(\Modules\Appointments\Models\BusinessAppointment::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(\Modules\OfficeHours\Models\BusinessSchedule::class);
    }
}
