<?php

namespace Modules\Tasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessTask extends Model
{
    protected $fillable = [
        'business_id',
        'title',
        'description',
        'status',
        'sort_order',
        'completed_at',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'completed_at' => 'datetime',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Businesses\Models\Business::class);
    }
}
