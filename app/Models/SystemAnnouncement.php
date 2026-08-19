<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SystemAnnouncement extends Model
{
    protected $fillable = [
        'title',
        'message',
        'type',
        'audience',
        'is_active',
        'starts_at',
        'ends_at',
        'dismissible',
        'priority',
        'action_label',
        'action_url',
        'created_by_user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'dismissible' => 'boolean',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function states(): HasMany
    {
        return $this->hasMany(UserAnnouncementState::class, 'system_announcement_id');
    }
}
