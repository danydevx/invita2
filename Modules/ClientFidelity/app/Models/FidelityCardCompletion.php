<?php

namespace Modules\ClientFidelity\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FidelityCardCompletion extends Model
{
    use HasFactory;

    protected $table = 'fidelity_card_completions';

    public $timestamps = false;

    protected $fillable = [
        'client_fidelity_card_id',
        'fidelity_reward_id',
        'client_name',
        'client_email',
        'client_phone',
        'visits_completed',
        'completed_by_user_id',
        'notes',
        'created_at',
    ];

    protected $casts = [
        'visits_completed' => 'integer',
        'created_at' => 'datetime',
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(ClientFidelityCard::class, 'client_fidelity_card_id');
    }

    public function reward(): BelongsTo
    {
        return $this->belongsTo(FidelityReward::class, 'fidelity_reward_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'completed_by_user_id');
    }
}
