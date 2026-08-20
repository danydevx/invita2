<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Listings\Enums\ListingType;
use Modules\Listings\Models\Listing;

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

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class, 'industry_id');
    }

    public function getModuleKeysAttribute(): array
    {
        return $this->moduleDefinitions->pluck('key')->toArray();
    }

    public function listingType(): ListingType
    {
        return match ($this->slug) {
            'barberia' => ListingType::BARBER_SHOP,
            'spa-belleza' => ListingType::BEAUTY_SALON,
            'clinica-medica' => ListingType::MEDICAL_CLINIC,
            default => ListingType::GENERIC,
        };
    }
}
