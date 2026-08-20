<?php

namespace Modules\ListingTeamMembers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class TeamMemberPosition extends Model
{
    protected $table = 'team_member_positions';

    protected $fillable = [
        'listing_id',
        'parent_id',
        'name',
        'slug',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function business(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class, 'listing_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(TeamMemberPosition::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(TeamMemberPosition::class, 'parent_id')->orderBy('sort_order');
    }

    public function teamMembers(): HasMany
    {
        return $this->hasMany(ListingTeamMember::class, 'position_id');
    }

    public static function generateUniqueSlug(int $businessId, string $name, ?int $excludeId = null): string
    {
        $slug = Str::slug($name, '-');
        $originalSlug = $slug;
        $count = 1;

        $query = static::where('listing_id', $businessId)->where('slug', $slug);
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        while ($query->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
            $query = static::where('listing_id', $businessId)->where('slug', $slug);
            if ($excludeId) {
                $query->where('id', '!=', $excludeId);
            }
        }

        return $slug;
    }
}
