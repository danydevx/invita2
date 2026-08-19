<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyAmenityProperty extends Model
{
    use HasFactory;

    protected $table = 'property_amenity_property';

    protected $fillable = [
        'property_id',
        'property_amenity_id',
        'value',
    ];

    protected $casts = [
        'value' => 'boolean',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function amenity(): BelongsTo
    {
        return $this->belongsTo(PropertyAmenity::class, 'property_amenity_id');
    }
}
