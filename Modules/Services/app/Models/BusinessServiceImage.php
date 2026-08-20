<?php

namespace Modules\Services\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessServiceImage extends Model
{
    protected $table = 'listing_service_images';

    protected $fillable = [
        'business_service_id',
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
        return $this->belongsTo(BusinessService::class, 'business_service_id');
    }
}
