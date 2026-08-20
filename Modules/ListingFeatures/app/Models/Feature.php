<?php

namespace Modules\ListingFeatures\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\Listings\Models\Listing;
use Modules\ListingLocations\Models\ListingLocation;

class Feature extends Model
{
    protected $fillable = [
        'category_id',
        'listing_id',
        'source_feature_id',
        'title',
        'description',
        'icon',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(FeatureCategory::class, 'category_id');
    }

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function sourceFeature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'source_feature_id');
    }

    public function clones(): HasMany
    {
        return $this->hasMany(Feature::class, 'source_feature_id');
    }

    public function listingFeatures(): HasMany
    {
        return $this->hasMany(ListingFeature::class, 'feature_id');
    }

    public function locations(): HasManyThrough
    {
        return $this->hasManyThrough(
            ListingLocation::class,
            ListingFeature::class,
            'feature_id',
            'id',
            'id',
            'location_id'
        );
    }

    public function isPredefined(): bool
    {
        return is_null($this->listing_id) && is_null($this->source_feature_id);
    }

    public function isCustom(): bool
    {
        return !is_null($this->listing_id) && is_null($this->source_feature_id);
    }

    public function isClone(): bool
    {
        return !is_null($this->source_feature_id);
    }
}
