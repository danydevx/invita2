<?php

namespace Modules\Features\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Businesses\Models\Business;
use Modules\Locations\Models\BusinessLocation;

class BusinessFeature extends Model
{
    protected $table = 'business_features';

    protected $fillable = [
        'business_id',
        'feature_id',
        'location_id',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(BusinessLocation::class, 'location_id');
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
