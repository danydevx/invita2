<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyValue extends Model
{
    use HasFactory;

    protected $table = 'property_values';

    protected $fillable = [
        'property_id',
        'property_field_id',
        'value_text',
        'value_number',
        'value_boolean',
        'value_date',
        'value_json',
    ];

    protected $casts = [
        'value_number' => 'decimal:4',
        'value_boolean' => 'boolean',
        'value_date' => 'date',
        'value_json' => 'array',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function propertyField(): BelongsTo
    {
        return $this->belongsTo(PropertyField::class, 'property_field_id');
    }

    public function getValue(): mixed
    {
        if ($this->value_text !== null) {
            return $this->value_text;
        }
        if ($this->value_number !== null) {
            return $this->value_number;
        }
        if ($this->value_boolean !== null) {
            return $this->value_boolean;
        }
        if ($this->value_date !== null) {
            return $this->value_date;
        }
        if ($this->value_json !== null) {
            return $this->value_json;
        }
        return null;
    }
}
