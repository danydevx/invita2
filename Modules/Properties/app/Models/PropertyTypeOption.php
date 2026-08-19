<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyTypeOption extends Model
{
    use HasFactory;

    protected $table = 'property_type_options';

    protected $fillable = [
        'property_field_id',
        'value',
        'label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(PropertyField::class, 'property_field_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
