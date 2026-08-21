<?php

namespace Modules\ListingAiChatbot\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;

class ListingAiSetting extends Model
{
    protected $table = 'listing_ai_settings';

    protected $fillable = [
        'listing_id',
        'preset_id',
        'provider',
        'api_key',
        'model',
        'embedding_model',
        'system_prompt',
        'chatbot_name',
        'chatbot_avatar',
        'personality',
        'response_length',
        'expandable_responses',
        'show_citations',
        'max_conversations_month',
        'max_messages_conversation',
        'max_tokens_response',
        'widget_color',
        'widget_theme',
        'is_enabled',
        'allow_reset_chat',
        'url_import_max_chars',
        'rag_min_similarity',
        'rag_max_results',
        'lead_capture_enabled',
        'lead_capture_trigger',
        'lead_capture_title',
        'lead_capture_description',
        'cta_enabled',
        'cta_primary_text',
        'cta_primary_url',
        'cta_secondary_text',
        'cta_secondary_url',
        'intent_cta',
        'additional_preset_ids',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'allow_reset_chat' => 'boolean',
        'expandable_responses' => 'boolean',
        'show_citations' => 'boolean',
        'max_conversations_month' => 'integer',
        'max_messages_conversation' => 'integer',
        'max_tokens_response' => 'integer',
        'url_import_max_chars' => 'integer',
        'rag_min_similarity' => 'float',
        'rag_max_results' => 'integer',
        'lead_capture_enabled' => 'boolean',
        'cta_enabled' => 'boolean',
        'additional_preset_ids' => 'array',
    ];

    protected $hidden = [
        'api_key',
    ];

    public function listing(): BelongsTo
    {
        return $this->belongsTo(\Modules\Listings\Models\Listing::class);
    }

    public function preset(): BelongsTo
    {
        return $this->belongsTo(ChatbotPreset::class);
    }

    public function setApiKeyAttribute($value): void
    {
        if (empty($value)) {
            $this->attributes['api_key'] = null;
            return;
        }

        if (str_contains($value, '::')) {
            $this->attributes['api_key'] = $value;
            return;
        }

        $this->attributes['api_key'] = Crypt::encryptString($value);
    }

    public function getApiKeyAttribute($value): ?string
    {
        if (!$value) {
            return null;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getDefaultSystemPrompt(): string
    {
        return "Eres un asistente virtual amigable y útil de {business_name}. Tu objetivo es ayudar a los clientes con información sobre productos, servicios, promociones y cualquier consulta relacionada con el negocio. Responde de manera clara, concisa y en español. Si no tienes información suficiente, indica que no estás seguro y sugiere contactar directamente al negocio.";
    }

    public function getRagMinSimilarity(): float
    {
        return $this->rag_min_similarity ?? 0.250;
    }

    public function getRagMaxResults(): int
    {
        return $this->rag_max_results ?? 5;
    }

    public function getAllActivePresets()
    {
        $presetIds = array_filter(array_merge(
            [$this->preset_id],
            $this->additional_preset_ids ?? []
        ));

        if (empty($presetIds)) {
            return collect();
        }

        return ChatbotPreset::whereIn('id', $presetIds)
            ->where('is_active', true)
            ->orderBy('is_system', 'desc')
            ->orderBy('name')
            ->get();
    }
}
