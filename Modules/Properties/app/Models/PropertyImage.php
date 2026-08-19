<?php

namespace Modules\Properties\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PropertyImage extends Model
{
    use HasFactory;

    protected $table = 'property_images';

    protected $fillable = [
        'property_id',
        'image_path',
        'alt_text',
        'caption',
        'is_main',
        'sort_order',
    ];

    protected $casts = [
        'is_main' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function getUrl(): string
    {
        return $this->image_path ? "/storage/{$this->image_path}" : '';
    }
}
