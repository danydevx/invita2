<?php

namespace Modules\ClientFidelity\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ClientFidelityCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'listing_id',
        'client_name',
        'client_email',
        'client_phone',
        'description',
        'max_visits',
        'current_visits',
        'public_code',
        'is_active',
        'completed_at',
        'reset_count',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'completed_at' => 'datetime',
        'max_visits' => 'integer',
        'current_visits' => 'integer',
        'reset_count' => 'integer',
    ];

    protected $appends = [
        'visits_remaining',
        'progress_percentage',
        'is_completed',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public static function generatePublicCode(): string
    {
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $code = '';
        $length = strlen($chars);

        do {
            $code = '';
            for ($i = 0; $i < 12; $i++) {
                $code .= $chars[random_int(0, $length - 1)];
            }
        } while (self::where('public_code', $code)->exists());

        return $code;
    }

    public function decrementVisit(): bool
    {
        if ($this->current_visits <= 0) {
            return false;
        }

        $this->decrement('current_visits');

        if ($this->current_visits <= 0) {
            $this->complete();
        }

        return true;
    }

    public function complete(): void
    {
        $this->update([
            'completed_at' => now(),
            'is_active' => false,
        ]);
    }

    public function reset(): void
    {
        $this->update([
            'current_visits' => $this->max_visits,
            'completed_at' => null,
            'is_active' => true,
            'reset_count' => $this->reset_count + 1,
        ]);
    }

    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->isCompleted();
    }

    public function getVisitsRemainingAttribute(): int
    {
        return max(0, $this->current_visits);
    }

    public function getProgressPercentageAttribute(): float
    {
        if ($this->max_visits <= 0) {
            return 100;
        }
        return round((($this->max_visits - $this->current_visits) / $this->max_visits) * 100, 1);
    }

    public function getQrUrl(): string
    {
        $business = $this->business;
        return route('public.fidelity.card', [
            'slug' => $business->slug,
            'public_code' => $this->public_code,
        ]);
    }
}
