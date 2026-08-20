<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotTopQuestion extends Model
{
    protected $table = 'chatbot_top_questions';

    protected $fillable = [
        'listing_id',
        'question',
        'question_hash',
        'times_asked',
        'last_asked_at',
        'low_confidence',
        'no_answer',
    ];

    protected $casts = [
        'last_asked_at' => 'date',
        'times_asked' => 'integer',
        'low_confidence' => 'boolean',
        'no_answer' => 'boolean',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public static function trackQuestion(int $businessId, string $question, bool $lowConfidence = false, bool $noAnswer = false): void
    {
        $hash = md5(strtolower(trim($question)));

        $existing = self::where('listing_id', $businessId)
            ->where('question_hash', $hash)
            ->first();

        if ($existing) {
            $existing->times_asked++;
            $existing->last_asked_at = now()->toDateString();
            if ($lowConfidence) {
                $existing->low_confidence = true;
            }
            if ($noAnswer) {
                $existing->no_answer = true;
            }
            $existing->save();
        } else {
            self::create([
                'listing_id' => $businessId,
                'question' => mb_substr(trim($question), 0, 500),
                'question_hash' => $hash,
                'times_asked' => 1,
                'last_asked_at' => now()->toDateString(),
                'low_confidence' => $lowConfidence,
                'no_answer' => $noAnswer,
            ]);
        }
    }

    public static function getTopQuestions(int $businessId, int $limit = 10): array
    {
        return self::where('listing_id', $businessId)
            ->orderBy('times_asked', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public static function getUnansweredQuestions(int $businessId, int $limit = 10): array
    {
        return self::where('listing_id', $businessId)
            ->where('no_answer', true)
            ->orderBy('times_asked', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public static function getLowConfidenceQuestions(int $businessId, int $limit = 10): array
    {
        return self::where('listing_id', $businessId)
            ->where('low_confidence', true)
            ->orderBy('times_asked', 'desc')
            ->limit($limit)
            ->get()
            ->toArray();
    }
}
