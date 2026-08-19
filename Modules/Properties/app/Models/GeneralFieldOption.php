<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GeneralFieldOption extends Model
{
    use HasFactory;

    protected $table = 'general_field_options';

    protected $fillable = [
        'general_field_id',
        'value',
        'label',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function field(): BelongsTo
    {
        return $this->belongsTo(GeneralField::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
