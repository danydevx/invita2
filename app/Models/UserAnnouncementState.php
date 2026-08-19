<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAnnouncementState extends Model
{
    protected $fillable = [
        'user_id',
        'system_announcement_id',
        'dismissed_at',
        'seen_at',
    ];

    protected $casts = [
        'dismissed_at' => 'datetime',
        'seen_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function announcement(): BelongsTo
    {
        return $this->belongsTo(SystemAnnouncement::class, 'system_announcement_id');
    }
}
