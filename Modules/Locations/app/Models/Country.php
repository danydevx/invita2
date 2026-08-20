<?php

namespace Modules\Locations\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name',
        'code',
        'currency',
        'currency_symbol',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function states(): HasMany
    {
        return $this->hasMany(State::class)->orderBy('name');
    }

    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class);
    }
}
