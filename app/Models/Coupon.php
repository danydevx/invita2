<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'value',
        'is_active',
        'starts_at',
        'ends_at',
        'usage_limit',
        'used_count',
        'applies_to_all_plans',
        'stripe_coupon_id',
        'stripe_promotion_code_id',
        'metadata',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'applies_to_all_plans' => 'boolean',
        'metadata' => 'array',
    ];

    public function plans(): BelongsToMany
    {
        return $this->belongsToMany(Plan::class)->withTimestamps();
    }
}
