<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemError extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'message',
        'file',
        'line',
        'url',
        'method',
        'ip_address',
        'user_agent',
        'exception_class',
        'trace',
        'context',
        'is_resolved',
        'resolved_at',
    ];

    protected $casts = [
        'context' => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
