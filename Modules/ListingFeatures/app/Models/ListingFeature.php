<?php

namespace Modules\ListingFeatures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Listings\Models\Listing;
use Modules\ListingLocations\Models\ListingLocation;

class ListingFeature extends Model
{
    protected $table = 'listing_features';

    protected $fillable = [
        'listing_id',
        'feature_id',
        'location_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(ListingLocation::class, 'location_id');
    }

    public function isForEntireBusiness(): bool
    {
        return is_null($this->location_id);
    }

    public function isForLocation(): bool
    {
        return !is_null($this->location_id);
    }
}
