<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AutomationRun extends Model
{
    protected $fillable = [
        'automation_id',
        'event_key',
        'status',
        'executed_at',
        'metadata',
    ];

    protected $casts = [
        'executed_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function automation(): BelongsTo
    {
        return $this->belongsTo(Automation::class);
    }
}
