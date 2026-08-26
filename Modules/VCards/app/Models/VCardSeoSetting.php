<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardSeoSetting extends Model
{
    protected $table = 'vcard_seo_settings';

    protected $fillable = [
        'vcard_id',
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

    protected $casts = [
        'allow_indexing' => 'boolean',
        'follow_links' => 'boolean',
        'include_in_sitemap' => 'boolean',
        'schema_enabled' => 'boolean',
        'settings' => 'array',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }
}
