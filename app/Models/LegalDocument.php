<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegalDocument extends Model
{
    protected $fillable = [
        'key',
        'title',
        'content',
        'version',
        'is_active',
        'is_required',
        'requires_reaccept',
    ];

    protected $casts = [
        'version' => 'integer',
        'is_active' => 'boolean',
        'is_required' => 'boolean',
        'requires_reaccept' => 'boolean',
    ];

    public function acceptances(): HasMany
    {
        return $this->hasMany(LegalAcceptance::class);
    }

    public function isAcceptanceValidFor(User $user): bool
    {
        $acceptance = $this->acceptances()
            ->where('user_id', $user->id)
            ->orderByDesc('accepted_at')
            ->first();

        if (! $acceptance) {
            return false;
        }

        if ($this->requires_reaccept) {
            return (int) $acceptance->version === (int) $this->version;
        }

        return true;
    }
}
