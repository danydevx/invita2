<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralFieldSection extends Model
{
    use HasFactory;

    protected $table = 'general_field_sections';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'sort_order',
        'is_active',
        'is_locked',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(GeneralField::class, 'general_field_section_id')->orderBy('sort_order');
    }

    public function activeFields(): HasMany
    {
        return $this->hasMany(GeneralField::class, 'general_field_section_id')
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(GeneralFieldTypeAssignment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
