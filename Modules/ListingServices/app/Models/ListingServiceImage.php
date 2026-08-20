<?php

namespace Modules\ListingServices\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingServiceImage extends Model
{
    protected $table = 'listing_service_images';

    protected $fillable = [
        'listing_service_id',
        'path',
        'filename',
        'original_name',
        'extension',
        'mime_type',
        'size',
        'sort_order',
        'is_primary',
    ];

    protected $casts = [
        'size' => 'integer',
        'sort_order' => 'integer',
        'is_primary' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(ListingService::class, 'listing_service_id');
    }
}
