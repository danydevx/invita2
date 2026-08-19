<?php

namespace Modules\Features\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Modules\Businesses\Models\Business;
use Modules\Locations\Models\BusinessLocation;

class Feature extends Model
{
    protected $fillable = [
        'category_id',
        'business_id',
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

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function sourceFeature(): BelongsTo
    {
        return $this->belongsTo(Feature::class, 'source_feature_id');
    }

    public function clones(): HasMany
    {
        return $this->hasMany(Feature::class, 'source_feature_id');
    }

    public function businessFeatures(): HasMany
    {
        return $this->hasMany(BusinessFeature::class, 'feature_id');
    }

    public function locations(): HasManyThrough
    {
        return $this->hasManyThrough(
            BusinessLocation::class,
            BusinessFeature::class,
            'feature_id',
            'id',
            'id',
            'location_id'
        );
    }

    public function isPredefined(): bool
    {
        return is_null($this->business_id) && is_null($this->source_feature_id);
    }

    public function isCustom(): bool
    {
        return !is_null($this->business_id) && is_null($this->source_feature_id);
    }

    public function isClone(): bool
    {
        return !is_null($this->source_feature_id);
    }
}
