<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'billing_period',
        'is_active',
        'sort_order',
        'features',
        'limits',
        'stripe_product_id',
        'stripe_price_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'features' => 'array',
        'limits' => 'array',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function featureFlags(): HasMany
    {
        return $this->hasMany(PlanFeatureFlag::class);
    }

    public function coupons(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Coupon::class)->withTimestamps();
    }
}
