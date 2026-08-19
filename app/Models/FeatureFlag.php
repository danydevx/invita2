<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeatureFlag extends Model
{
    protected $fillable = [
        'key',
        'name',
        'description',
        'type',
        'default_value',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function planOverrides(): HasMany
    {
        return $this->hasMany(PlanFeatureFlag::class);
    }

    public function userOverrides(): HasMany
    {
        return $this->hasMany(UserFeatureFlag::class);
    }
}
