<?php

namespace Modules\Features\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function features(): HasMany
    {
        return $this->hasMany(Feature::class, 'category_id');
    }

    public function predefinedFeatures(): HasMany
    {
        return $this->hasMany(Feature::class, 'category_id')
            ->whereNull('business_id')
            ->whereNull('source_feature_id');
    }
}
