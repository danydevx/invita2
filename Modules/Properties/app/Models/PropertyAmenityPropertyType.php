<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyAmenityPropertyType extends Model
{
    use HasFactory;

    protected $table = 'property_amenity_property_type';

    protected $fillable = [
        'property_type_id',
        'property_amenity_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function amenity(): BelongsTo
    {
        return $this->belongsTo(PropertyAmenity::class, 'property_amenity_id');
    }
}
