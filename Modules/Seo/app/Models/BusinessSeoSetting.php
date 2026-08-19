<?php

namespace Modules\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessSeoSetting extends Model
{
    protected $fillable = [
        'business_id',
        'seo_title',
        'seo_description',
        'focus_keyword',
        'allow_indexing',
        'follow_links',
        'include_in_sitemap',
        'canonical_url',
        'og_title',
        'og_description',
        'og_image',
        'og_image_alt',
        'schema_enabled',
        'schema_type',
        'settings',
    ];

    protected function casts(): array
    {
        return [
            'allow_indexing' => 'boolean',
            'follow_links' => 'boolean',
            'include_in_sitemap' => 'boolean',
            'schema_enabled' => 'boolean',
            'settings' => 'array',
        ];
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }
}
