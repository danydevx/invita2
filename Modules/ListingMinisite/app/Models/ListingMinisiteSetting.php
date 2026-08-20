<?php

namespace Modules\ListingMinisite\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingMinisiteSetting extends Model
{

    protected $table = 'listing_minisite_settings';

    protected $fillable = [
        'listing_id',
        'theme_key',
        'hero_layout',
        'hero_title',
        'hero_subtitle',
        'hero_background_image',
        'hero_show_social',
        'footer_text',
        'footer_show_social',
        'is_active',
    ];

    protected $casts = [
        'footer_show_social' => 'boolean',
        'hero_show_social' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(ListingMinisiteSection::class, 'listing_id');
    }

    public static function getHeroLayouts(): array
    {
        return [
            'left' => 'Izquierda',
            'center' => 'Centrado',
            'right' => 'Derecha',
        ];
    }
}