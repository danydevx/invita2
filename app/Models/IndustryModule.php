<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndustryModule extends Model
{
    protected $table = 'industry_modules';

    protected $fillable = [
        'industry_id',
        'module_definition_id',
    ];

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class);
    }

    public function moduleDefinition(): BelongsTo
    {
        return $this->belongsTo(BusinessModuleDefinition::class, 'module_definition_id');
    }
}
