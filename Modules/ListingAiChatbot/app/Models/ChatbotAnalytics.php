<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotAnalytics extends Model
{
    protected $table = 'chatbot_analytics';

    protected $fillable = [
        'listing_id',
        'date',
        'conversations_count',
        'messages_count',
        'tokens_used',
        'total_latency_ms',
        'estimated_cost',
        'errors_count',
    ];

    protected $casts = [
        'date' => 'date',
        'conversations_count' => 'integer',
        'messages_count' => 'integer',
        'tokens_used' => 'integer',
        'total_latency_ms' => 'integer',
        'estimated_cost' => 'decimal:6',
        'errors_count' => 'integer',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public static function incrementStats(int $businessId, int $messages = 1, int $tokens = 0, bool $isError = false, int $latencyMs = 0): void
    {
        $today = now()->toDateString();

        $analytics = self::firstOrCreate(
            ['listing_id' => $businessId, 'date' => $today],
            ['conversations_count' => 0, 'messages_count' => 0, 'tokens_used' => 0, 'total_latency_ms' => 0, 'estimated_cost' => 0, 'errors_count' => 0]
        );

        $analytics->messages_count += $messages;
        $analytics->tokens_used += $tokens;
        $analytics->total_latency_ms += $latencyMs;
        $analytics->estimated_cost += self::calculateCost($tokens);
        if ($isError) {
            $analytics->errors_count++;
        }
        $analytics->save();
    }

    public static function calculateCost(int $tokens): float
    {
        $inputCost = ($tokens * 0.15) / 1000000;
        $outputCost = ($tokens * 0.60) / 1000000;
        return $inputCost + $outputCost;
    }

    public static function incrementConversations(int $businessId): void
    {
        $today = now()->toDateString();

        $analytics = self::firstOrCreate(
            ['listing_id' => $businessId, 'date' => $today],
            ['conversations_count' => 0, 'messages_count' => 0, 'tokens_used' => 0, 'errors_count' => 0]
        );

        $analytics->conversations_count++;
        $analytics->save();
    }

    public static function getStats(int $businessId, string $period = '30days'): array
    {
        $from = match ($period) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            'year' => now()->subYear(),
            default => now()->subDays(30),
        };

        return self::where('listing_id', $businessId)
            ->where('date', '>=', $from->toDateString())
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    public static function getTotals(int $businessId, string $period = '30days'): array
    {
        $from = match ($period) {
            '7days' => now()->subDays(7),
            '30days' => now()->subDays(30),
            '90days' => now()->subDays(90),
            'year' => now()->subYear(),
            default => now()->subDays(30),
        };

        $result = self::where('listing_id', $businessId)
            ->where('date', '>=', $from->toDateString())
            ->selectRaw('SUM(conversations_count) as total_conversations, SUM(messages_count) as total_messages, SUM(tokens_used) as total_tokens, SUM(total_latency_ms) as total_latency_ms, SUM(estimated_cost) as total_cost, SUM(errors_count) as total_errors')
            ->first();

        return [
            'total_conversations' => $result->total_conversations ?? 0,
            'total_messages' => $result->total_messages ?? 0,
            'total_tokens' => $result->total_tokens ?? 0,
            'total_latency_ms' => $result->total_latency_ms ?? 0,
            'total_cost' => $result->total_cost ?? 0,
            'total_errors' => $result->total_errors ?? 0,
        ];
    }
}
