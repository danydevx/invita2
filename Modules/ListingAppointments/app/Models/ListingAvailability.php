<?php

namespace Modules\ListingAppointments\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ListingAvailability extends Model
{
    protected $table = 'listing_availability';

    protected $fillable = [
        'listing_id',
        'day_of_week',
        'is_available',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'slots_per_slot',
    ];

    protected $casts = [
        'day_of_week' => 'integer',
        'is_available' => 'boolean',
        'start_time' => 'string',
        'end_time' => 'string',
        'slot_duration_minutes' => 'integer',
        'slots_per_slot' => 'integer',
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

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function exceptions(): HasMany
    {
        return $this->hasMany(ListingAvailabilityException::class, 'listing_id', 'listing_id');
    }

    public static function dayName(int $dayOfWeek): string
    {
        return self::DAY_NAMES[$dayOfWeek] ?? '';
    }

    public static function dayShortName(int $dayOfWeek): string
    {
        return self::DAY_SHORT_NAMES[$dayOfWeek] ?? '';
    }
}
