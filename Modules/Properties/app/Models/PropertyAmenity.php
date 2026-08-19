<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PropertyAmenity extends Model
{
    use HasFactory;

    protected $table = 'property_amenities';

    protected $fillable = [
        'key',
        'name',
        'icon',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function propertyTypes(): BelongsToMany
    {
        return $this->belongsToMany(
            PropertyType::class,
            'property_amenity_property_type',
            'property_amenity_id',
            'property_type_id'
        )->withPivot(['sort_order', 'is_active'])
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('property_amenities.is_active', true);
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sort_order');
    }
}
