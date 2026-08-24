<?php

namespace Modules\ListingTasks\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ListingTask extends Model
{

    protected $table = 'listing_tasks';

    protected $fillable = [
        'listing_id',
        'title',
        'description',
        'status',
        'sort_order',
        'completed_at',
        'archived_at',
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'completed_at' => 'datetime',
        'archived_at' => 'datetime',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }
}