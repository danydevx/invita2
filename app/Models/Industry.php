<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Businesses\Enums\BusinessType;

class Industry extends Model
{
    use HasFactory;

    protected $table = 'industries';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function moduleDefinitions(): BelongsToMany
    {
        return $this->belongsToMany(
            BusinessModuleDefinition::class,
            'industry_modules',
            'industry_id',
            'module_definition_id'
        )->withTimestamps();
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class, 'industry_id');
    }

    public function getModuleKeysAttribute(): array
    {
        return $this->moduleDefinitions->pluck('key')->toArray();
    }

    public function businessType(): BusinessType
    {
        return match ($this->slug) {
            'barberia' => BusinessType::BARBER_SHOP,
            'spa-belleza' => BusinessType::BEAUTY_SALON,
            'clinica-medica' => BusinessType::MEDICAL_CLINIC,
            default => BusinessType::GENERIC,
        };
    }
}
