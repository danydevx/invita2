<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PropertyFieldSection extends Model
{
    use HasFactory;

    protected $table = 'property_field_sections';

    protected $fillable = [
        'property_type_id',
        'name',
        'description',
        'sort_order',
        'is_active',
        'is_general',
        'general_field_section_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_general' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = ['is_locked'];

    public function getIsLockedAttribute(): bool
    {
        return $this->generalFieldSection?->is_locked ?? false;
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function generalFieldSection(): BelongsTo
    {
        return $this->belongsTo(GeneralFieldSection::class);
    }

    public function fields(): HasMany
    {
        return $this->hasMany(PropertyField::class, 'section_id')->orderBy('sort_order');
    }

    public function activeFields(): HasMany
    {
        return $this->hasMany(PropertyField::class, 'section_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeExclusive($query)
    {
        return $query->where('is_general', false);
    }

    public function scopeInherited($query)
    {
        return $query->where('is_general', true);
    }
}
