<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessModuleDefinition extends Model
{
    use HasFactory;

    protected $table = 'business_module_definitions';

    protected $fillable = [
        'key',
        'name',
        'description',
        'icon',
        'image',
        'sort_order',
        'is_active',
        'is_premium',
        'has_settings',
        'settings_url',
        'show_in_menu',
        'menu_title',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_premium' => 'boolean',
        'has_settings' => 'boolean',
        'show_in_menu' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function planModules(): HasMany
    {
        return $this->hasMany(PlanBusinessModule::class, 'module_definition_id');
    }

    public function businessModules(): HasMany
    {
        return $this->hasMany(BusinessModule::class, 'module_definition_id');
    }

    public function industries(): BelongsToMany
    {
        return $this->belongsToMany(
            Industry::class,
            'industry_modules',
            'module_definition_id',
            'industry_id'
        )->withTimestamps();
    }
}
