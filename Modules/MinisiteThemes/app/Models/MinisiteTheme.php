<?php

namespace Modules\MinisiteThemes\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Businesses\Models\Business;

class MinisiteTheme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview_image',
        'css_variables',
        'layout_config',
        'is_active',
    ];

    protected $casts = [
        'css_variables' => 'array',
        'layout_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }

    public static function getByBusinessType(string $businessType): ?self
    {
        $mapping = [
            'barber_shop' => 'modern',
            'beauty_salon' => 'elegant',
            'spa' => 'elegant',
            'tattoo_studio' => 'bold',
            'dentist' => 'professional',
            'medical_clinic' => 'professional',
            'doctor' => 'professional',
            'physiotherapist' => 'professional',
            'psychologist' => 'professional',
            'nutritionist' => 'professional',
            'veterinarian' => 'friendly',
            'generic' => 'modern',
        ];

        $slug = $mapping[$businessType] ?? 'modern';

        return static::where('slug', $slug)->where('is_active', true)->first();
    }
}
