<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderSetting extends Model
{

    protected $table = 'order_settings';

    protected $fillable = [
        'listing_id',
        'order_type',
        'delivery_radius_km',
        'delivery_fee_base',
        'delivery_fee_per_km',
        'free_delivery_threshold',
        'min_order_amount',
        'whatsapp_number',
        'is_active',
    ];

    protected $casts = [
        'delivery_radius_km' => 'decimal:2',
        'delivery_fee_base' => 'decimal:2',
        'delivery_fee_per_km' => 'decimal:2',
        'free_delivery_threshold' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function calculateDeliveryFee(float $distanceKm): float
    {
        if ($this->free_delivery_threshold && $this->free_delivery_threshold > 0) {
            return 0;
        }

        $fee = $this->delivery_fee_base;

        $extraDistance = max(0, $distanceKm - 1);
        $fee += $extraDistance * $this->delivery_fee_per_km;

        return round($fee, 2);
    }

    public function canDeliverTo(float $distanceKm): bool
    {
        return $distanceKm <= $this->delivery_radius_km;
    }

    public static function getForBusiness(int $businessId): ?self
    {
        return static::where('listing_id', $businessId)->first();
    }
}
