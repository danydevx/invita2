<?php

namespace Modules\OfficeHours\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Businesses\Models\Business;

class BusinessSchedule extends Model
{
    protected $table = 'business_schedules';

    protected $fillable = [
        'business_id',
        'business_location_id',
        'name',
        'days_of_week',
        'opening_time',
        'closing_time',
        'lunch_start_time',
        'lunch_end_time',
        'is_active',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'is_active' => 'boolean',
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

    public const DAY_SHORT_NAMES = [
        0 => 'Dom',
        1 => 'Lun',
        2 => 'Mar',
        3 => 'Mié',
        4 => 'Jue',
        5 => 'Vie',
        6 => 'Sáb',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(Business::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(\Modules\Locations\Models\BusinessLocation::class, 'business_location_id');
    }

    public static function dayName(int $dayOfWeek): string
    {
        return self::DAY_NAMES[$dayOfWeek] ?? '';
    }

    public static function dayShortName(int $dayOfWeek): string
    {
        return self::DAY_SHORT_NAMES[$dayOfWeek] ?? '';
    }

    public function getDaysDisplayAttribute(): string
    {
        if (empty($this->days_of_week)) {
            return 'Todos los días';
        }

        $days = $this->days_of_week;
        sort($days);

        if ($days == [0, 1, 2, 3, 4, 5, 6]) {
            return 'Todos los días';
        }

        if ($days == [1, 2, 3, 4, 5]) {
            return 'Lunes a Viernes';
        }

        if ($days == [6, 0]) {
            return 'Fines de semana';
        }

        $names = array_map(fn($d) => self::DAY_SHORT_NAMES[$d], $days);
        return implode(', ', $names);
    }

    public function getTimeDisplayAttribute(): string
    {
        $opening = substr($this->opening_time, 0, 5);
        $closing = substr($this->closing_time, 0, 5);

        if ($this->lunch_start_time && $this->lunch_end_time) {
            $lunchStart = substr($this->lunch_start_time, 0, 5);
            $lunchEnd = substr($this->lunch_end_time, 0, 5);
            return "{$opening} - {$closing} ({$lunchStart} - {$lunchEnd} cerrado)";
        }

        return "{$opening} - {$closing}";
    }
}
