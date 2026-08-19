<?php

namespace Modules\Orders\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderPickupLocation extends Model
{
    protected $fillable = [
        'order_id',
        'location_id',
        'location_name',
        'location_address',
        'pickup_time',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
