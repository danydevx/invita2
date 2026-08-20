<?php

namespace Modules\ListingModules\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingModule extends Model
{
    protected $table = 'listing_modules';

    protected $fillable = [
        'listing_id',
        'module_definition_id',
        'module_key',
        'module_name',
        'is_enabled',
        'settings',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'settings' => 'array',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class, 'listing_id');
    }

    public function moduleDefinition(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ModuleDefinition::class, 'module_definition_id');
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }
}
