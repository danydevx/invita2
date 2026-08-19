<?php

namespace Modules\About\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Businesses\Models\Business;

class BusinessAbout extends Model
{
    protected $table = 'business_abouts';

    protected $fillable = [
        'business_id',
        'title',
        'subtitle',
        'description',
        'image_path',
        'logo_path',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }
}
