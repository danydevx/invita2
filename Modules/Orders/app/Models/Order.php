<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Modules\Listings\Models\Listing;
use Modules\Orders\Enums\OrderStatus;
use Modules\Orders\Enums\OrderType;

class Order extends Model
{

    protected $table = 'orders';

    protected $fillable = [
        'listing_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'order_type',
        'subtotal',
        'delivery_fee',
        'total',
        'distance_km',
        'status',
        'notes',
    ];

    protected $casts = [
        'order_type' => OrderType::class,
        'status' => OrderStatus::class,
        'subtotal' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
        'total' => 'decimal:2',
        'distance_km' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber($order->listing_id);
            }
        });
    }

    public static function generateOrderNumber(int $businessId): string
    {
        $year = date('Y');
        $prefix = "ORD-{$year}-";

        $lastOrder = static::where('listing_id', $businessId)
            ->whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $lastNumber = 0;
        if ($lastOrder && preg_match('/' . $prefix . '(\d+)/', $lastOrder->order_number, $matches)) {
            $lastNumber = (int) $matches[1];
        }

        return $prefix . str_pad($lastNumber + 1, 5, '0', STR_PAD_LEFT);
    }

    public function business(): BelongsTo
    {
        return $this->belongsTo(Listing::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function deliveryAddress(): HasOne
    {
        return $this->hasOne(OrderDeliveryAddress::class);
    }

    public function pickupLocation(): HasOne
    {
        return $this->hasOne(OrderPickupLocation::class);
    }

    public function calculateTotals(): void
    {
        $this->subtotal = $this->items()->sum('subtotal');
        $this->total = $this->subtotal + $this->delivery_fee;
        $this->save();
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status->label();
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status->color();
    }
}
