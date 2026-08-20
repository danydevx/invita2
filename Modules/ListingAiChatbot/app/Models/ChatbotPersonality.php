<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ChatbotPersonality extends Model
{
    protected $table = 'chatbot_personalities';

    protected $fillable = [
        'key',
        'display_name',
        'description',
        'system_prompt_hint',
        'default_temperature',
        'default_response_length',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'default_temperature' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeSorted(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    public static function getActiveForSelect()
    {
        return self::active()->sorted()->get();
    }

    public static function keyExists(string $key, ?int $exceptId = null): bool
    {
        $query = self::where('key', $key);
        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }
        return $query->exists();
    }
}
