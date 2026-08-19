<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanBusinessModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'module_definition_id',
        'module_key',
        'is_enabled',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
    ];

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function moduleDefinition(): BelongsTo
    {
        return $this->belongsTo(BusinessModuleDefinition::class, 'module_definition_id');
    }
}
