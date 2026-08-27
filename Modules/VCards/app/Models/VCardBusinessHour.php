<?php

namespace Modules\VCards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VCardBusinessHour extends Model
{
    protected $table = 'vcard_business_hours';

    protected $fillable = [
        'vcard_id',
        'day_of_week',
        'is_open',
        'opening_time',
        'closing_time',
        'lunch_start_time',
        'lunch_end_time',
        'sort_order',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_open' => 'boolean',
        'sort_order' => 'integer',
    ];

    public const DAY_NAMES = [
        0 => 'Domingo',
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
    ];

    public function vcard(): BelongsTo
    {
        return $this->belongsTo(VCard::class, 'vcard_id');
    }

    public function getDayNameAttribute(): string
    {
        return self::DAY_NAMES[$this->day_of_week] ?? '';
    }

    public function getTimeRangeAttribute(): string
    {
        if (!$this->is_open) {
            return 'Cerrado';
        }

        $opening = substr($this->opening_time, 0, 5);
        $closing = substr($this->closing_time, 0, 5);

        if ($this->lunch_start_time && $this->lunch_end_time) {
            $lunchStart = substr($this->lunch_start_time, 0, 5);
            $lunchEnd = substr($this->lunch_end_time, 0, 5);
            return "{$opening} - {$closing}";
        }

        return "{$opening} - {$closing}";
    }
}
