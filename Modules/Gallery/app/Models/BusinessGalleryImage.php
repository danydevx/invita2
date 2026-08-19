<?php

namespace Modules\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessGalleryImage extends Model
{
    protected $fillable = [
        'business_id',
        'business_gallery_id',
        'business_location_id',
        'path',
        'filename',
        'original_name',
        'extension',
        'mime_type',
        'size',
        'title',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }

    public function gallery(): BelongsTo
    {
        return $this->belongsTo(BusinessGallery::class, 'business_gallery_id');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\Locations\Models\BusinessLocation::class, 'business_location_id');
    }
}
