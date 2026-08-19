<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralFieldTypeAssignment extends Model
{
    use HasFactory;

    protected $table = 'general_field_type_assignments';

    protected $fillable = [
        'property_type_id',
        'general_field_section_id',
        'custom_settings',
        'sort_order',
    ];

    protected $casts = [
        'custom_settings' => 'array',
        'sort_order' => 'integer',
    ];

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function generalFieldSection(): BelongsTo
    {
        return $this->belongsTo(GeneralFieldSection::class);
    }
}
