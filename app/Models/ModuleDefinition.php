<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModuleDefinition extends Model
{
    use HasFactory;

    protected $table = 'listing_module_definitions';

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

    public function listingModules(): HasMany
    {
        return $this->hasMany(\Modules\ListingModules\Models\ListingModule::class, 'module_definition_id');
    }
}
