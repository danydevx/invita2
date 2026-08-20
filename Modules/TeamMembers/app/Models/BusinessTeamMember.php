<?php

namespace Modules\TeamMembers\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessTeamMember extends Model
{
    protected $table = 'listing_team_members';

    protected $fillable = [
        'listing_id',
        'position_id',
        'name',
        'email',
        'phone',
        'bio',
        'image',
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

    public function position(): BelongsTo
    {
        return $this->belongsTo(TeamMemberPosition::class, 'position_id');
    }
}
