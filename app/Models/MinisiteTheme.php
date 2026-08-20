<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Listings\Models\Listing;

class MinisiteTheme extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'preview_image',
        'css_variables',
        'layout_config',
        'section_config',
        'is_active',
    ];

    protected $casts = [
        'css_variables' => 'array',
        'layout_config' => 'array',
        'section_config' => 'array',
        'is_active' => 'boolean',
    ];

    public function listings(): HasMany
    {
        return $this->hasMany(Listing::class);
    }

    public static function getByListingType(string $listingType): ?self
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
            'wedding' => 'elegant',
            'birthday' => 'festive',
            'baby_shower' => 'playful',
            'corporate' => 'professional',
            'graduation' => 'celebratory',
            'generic' => 'modern',
        ];

        $slug = $mapping[$listingType] ?? 'modern';

        return static::where('slug', $slug)->where('is_active', true)->first();
    }
}
