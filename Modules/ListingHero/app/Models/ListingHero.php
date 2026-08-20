<?php

namespace Modules\ListingHero\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Listings\Models\Listing;

class ListingHero extends Model
{
    protected $table = 'listing_heroes';

    protected $fillable = [
        'listing_id',
        'title',
        'subtitle',
        'text_aux',
        'background_type',
        'background_color',
        'background_gradient_start',
        'background_gradient_end',
        'background_image_path',
        'alignment',
        'buttons',
        'social_links',
        'show_contact_info',
        'show_social_links',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'buttons' => 'array',
        'social_links' => 'array',
        'show_contact_info' => 'boolean',
        'show_social_links' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function getBackgroundStyle(): string
    {
        return match ($this->background_type) {
            'color' => "background-color: {$this->background_color}",
            'gradient' => "background: linear-gradient(135deg, {$this->background_gradient_start} 0%, {$this->background_gradient_end} 100%)",
            'image' => "background-image: url({$this->background_image_path}); background-size: cover; background-position: center",
            default => "background: linear-gradient(135deg, #1a1a2e 0%, #6B7280 100%)",
        };
    }
}
