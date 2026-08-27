<?php

namespace Modules\ListingAiChatbot\Services;

use Modules\ListingAiChatbot\Models\ListingAiSetting;
use Modules\ListingAiChatbot\Models\AiConversation;
use Modules\ListingAiChatbot\Models\AiMessage;
use Modules\ListingAiChatbot\Models\ChatbotAnalytics;
use Modules\ListingAiChatbot\Models\ChatbotTopQuestion;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class AiChatbotService
{
    private ListingAiSetting $settings;
    private VectorStoreService $vectorStore;

    public function __construct(ListingAiSetting $settings)
    {
        $this->settings = $settings;
        $this->vectorStore = new VectorStoreService($settings);
    }

    public function chat(string $userMessage, string $sessionId, ?string $ipAddress = null, ?string $userAgent = null, ?string $deviceType = null): array
    {
        if (!$this->settings->is_enabled) {
            return [
                'success' => false,
                'error' => 'Chatbot is disabled',
                'message' => null,
            ];
        }

        if (!$this->settings->api_key) {
            return [
                'success' => false,
                'error' => 'API key not configured',
                'message' => null,
            ];
        }

        if (!$this->checkLimits($sessionId)) {
            return [
                'success' => false,
                'error' => 'Monthly limit reached',
                'message' => 'Has alcanzado el límite de conversaciones para este mes. Por favor, intenta más tarde.',
            ];
        }

        $conversation = $this->getOrCreateConversation($sessionId, $ipAddress, $userAgent, $deviceType ?? null);

        if ($conversation->messages_count >= $this->settings->max_messages_conversation) {
            return [
                'success' => false,
                'error' => 'Conversation limit reached',
                'message' => 'Has alcanzado el límite de mensajes en esta conversación. Inicia una nueva conversación.',
            ];
        }

        $allPresets = $this->settings->getAllActivePresets();
        $allContextIds = [];
        foreach ($allPresets as $preset) {
            if (!empty($preset->context_ids)) {
                $allContextIds = array_merge($allContextIds, $preset->context_ids);
            }
        }

        if (!empty($allContextIds)) {
            $contextChunks = $this->vectorStore->searchSimilarInContexts(
                $userMessage,
                array_unique($allContextIds),
                $this->settings->getRagMaxResults(),
                $this->settings->getRagMinSimilarity()
            );
        } else {
            $contextChunks = $this->vectorStore->searchSimilar(
                $userMessage,
                $this->settings->getRagMaxResults(),
                $this->settings->getRagMinSimilarity()
            );
        }

        $conversationHistory = $this->getConversationHistory($conversation, 10);

        $systemPrompt = $this->buildSystemPrompt($contextChunks);

        $messages = $this->buildMessages($systemPrompt, $conversationHistory, $userMessage);

        try {
            $response = $this->callOpenAI($messages);
        } catch (\Exception $e) {
            Log::error('AI Chat error', [
                'listing_id' => $this->settings->listing_id,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'error' => 'AI service error',
                'message' => 'Disculpa, estoy teniendo problemas para responder. Por favor, intenta de nuevo.',
            ];
        }

        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
            'tokens_used' => $this->estimateTokens($userMessage),
        ]);

        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $response['content'],
            'tokens_used' => $response['tokens'] ?? 0,
        ]);

        $conversation->incrementMessagesCount();

        ChatbotAnalytics::incrementStats($this->settings->listing_id, 2, $response['tokens'] ?? 0);
        ChatbotTopQuestion::trackQuestion($this->settings->listing_id, $userMessage);

        $sources = [];
        if (!empty($contextChunks) && ($this->settings->show_citations ?? true)) {
            foreach ($contextChunks as $chunk) {
                $sources[] = [
                    'type' => $chunk['source_type'],
                    'text' => mb_substr($chunk['chunk_text'], 0, 150) . (mb_strlen($chunk['chunk_text']) > 150 ? '...' : ''),
                    'similarity' => $chunk['similarity'],
                ];
            }
        }

        return [
            'success' => true,
            'error' => null,
            'message' => $response['content'],
            'tokens' => $response['tokens'] ?? 0,
            'expandable_responses' => $this->settings->expandable_responses ?? true,
            'show_citations' => $this->settings->show_citations ?? true,
            'sources' => $sources,
        ];
    }

    public function checkLimits(string $sessionId): bool
    {
        $startOfMonth = Carbon::now()->startOfMonth();

        $conversationsThisMonth = AiConversation::where('listing_id', $this->settings->listing_id)
            ->where('started_at', '>=', $startOfMonth)
            ->count();

        return $conversationsThisMonth < $this->settings->max_conversations_month;
    }

    public function getConversationHistory(AiConversation $conversation, int $limit = 50): array
    {
        return $conversation->messages()
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->reverse()
            ->values()
            ->map(fn($msg) => [
                'role' => $msg->role,
                'content' => $msg->content,
            ])
            ->toArray();
    }

    public function getSettings(): ListingAiSetting
    {
        return $this->settings;
    }

    public function reindexContent(): array
    {
        return $this->vectorStore->reindexBusiness();
    }

    private function getOrCreateConversation(string $sessionId, ?string $ipAddress, ?string $userAgent, ?string $deviceType = null): AiConversation
    {
        $conversation = AiConversation::where('listing_id', $this->settings->listing_id)
            ->where('session_id', $sessionId)
            ->first();

        if (!$conversation) {
            $geoData = ['country' => 'Unknown', 'city' => 'Unknown', 'country_code' => 'XX'];
            if ($ipAddress) {
                $geoService = new GeoLocationService();
                $geoData = $geoService->resolve($ipAddress);
            }

            $conversation = AiConversation::create([
                'listing_id' => $this->settings->listing_id,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'device_type' => $deviceType,
                'country' => $geoData['country'],
                'city' => $geoData['city'],
                'country_code' => $geoData['country_code'],
                'messages_count' => 0,
                'started_at' => now(),
                'last_activity_at' => now(),
            ]);

            ChatbotAnalytics::incrementConversations($this->settings->listing_id);
        }

        return $conversation;
    }

    private function buildSystemPrompt(array $contextChunks): string
    {
        $listing = $this->settings->listing;
        $allPresets = $this->settings->getAllActivePresets();
        $mainPreset = $allPresets->first();

        if ($mainPreset && $mainPreset->system_prompt_template) {
            $systemPrompt = $mainPreset->system_prompt_template;
            $systemPrompt = str_replace('{business_name}', $listing->name ?? 'este negocio', $systemPrompt);
            $systemPrompt = str_replace('{greeting_addition}', '', $systemPrompt);
        } else {
            $systemPrompt = $this->settings->system_prompt ?: $this->settings->getDefaultSystemPrompt();
            $systemPrompt = str_replace('{business_name}', $listing->name ?? 'este negocio', $systemPrompt);
        }

        if ($allPresets->count() > 1) {
            $additionalPrompts = [];
            foreach ($allPresets->skip(1) as $preset) {
                if ($preset->system_prompt_template) {
                    $prompt = $preset->system_prompt_template;
                    $prompt = str_replace('{business_name}', $listing->name ?? 'este negocio', $prompt);
                    $prompt = str_replace('{greeting_addition}', '', $prompt);
                    $additionalPrompts[] = $prompt;
                }
            }
            if (!empty($additionalPrompts)) {
                $systemPrompt .= "\n\nInformación adicional del negocio:\n" . implode("\n\n---\n\n", $additionalPrompts);
            }
        }

        $personalityInstructions = $this->getPersonalityInstructions();
        if ($personalityInstructions) {
            $systemPrompt .= "\n\n" . $personalityInstructions;
        }

        $responseLengthInstructions = $this->getResponseLengthInstructions();
        if ($responseLengthInstructions) {
            $systemPrompt .= "\n\n" . $responseLengthInstructions;
        }

        if (!empty($contextChunks)) {
            $contextText = "\n\nInformación de referencia:\n";
            foreach ($contextChunks as $chunk) {
                $contextText .= "- [{$chunk['source_type']}] {$chunk['chunk_text']}\n";
            }
            $systemPrompt .= $contextText;
        }

        return $systemPrompt;
    }

    private function getPersonalityInstructions(): string
    {
        $personality = $this->settings->personality ?? 'friendly';

        return match ($personality) {
            'professional' => "Estilo de comunicación: Profesional y formal. Usa lenguaje técnico apropiado. Sé directo y orientado a soluciones.",
            'friendly' => "Estilo de comunicación: Amigable y cercano. Usa un tono cálido y accesible. Haz que el usuario se sienta cómodo.",
            'formal' => "Estilo de comunicación: Formal y respetuoso. Usa vocabulario cuidado y estructuras gramaticales correctas.",
            'casual' => "Estilo de comunicación: Casual y relajado. Usa un tono conversacional, como si hablaras con un amigo.",
            default => '',
        };
    }

    private function getResponseLengthInstructions(): string
    {
        $responseLength = $this->settings->response_length ?? 'medium';

        return match ($responseLength) {
            'short' => "Responde de manera CONCISA y directa. Idealmente en 1-3 oraciones. Ve al grano.",
            'medium' => "Responde de manera EQUILIBRADA. Idealmente en 2-5 oraciones. Combina claridad con достаточная información.",
            'long' => "Responde de manera DETALLADA y exhaustiva. Incluye ejemplos, contexto adicional y explicaciones completas cuando sea necesario.",
            default => '',
        };
    }

    private function buildMessages(string $systemPrompt, array $history, string $userMessage): array
    {
        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($history as $msg) {
            $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        return $messages;
    }

    private function callOpenAI(array $messages): array
    {
        $temperature = 0.7;
        $preset = $this->settings->preset;
        if ($preset && isset($preset->configuration['temperature'])) {
            $temperature = (float) $preset->configuration['temperature'];
        }

        $response = Http::withToken($this->settings->api_key)
            ->timeout(60)
            ->post('https://api.openai.com/v1/chat/completions', [
                'model' => $this->settings->model,
                'messages' => $messages,
                'max_tokens' => $this->settings->max_tokens_response,
                'temperature' => $temperature,
            ]);

        if ($response->failed()) {
            Log::error('OpenAI API error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $data = $response->json();

        return [
            'content' => $data['choices'][0]['message']['content'] ?? '',
            'tokens' => $data['usage']['total_tokens'] ?? 0,
        ];
    }

    private function estimateTokens(string $text): int
    {
        return (int) (strlen($text) / 4);
    }

    public function streamChat(string $userMessage, string $sessionId, ?string $ipAddress = null, ?string $userAgent = null, ?string $deviceType = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$this->settings->is_enabled) {
            return response()->stream(function () {
                echo "data: " . json_encode(['success' => false, 'error' => 'Chatbot is disabled']) . "\n\n";
            }, 200, ['Content-Type' => 'text/event-stream']);
        }

        if (!$this->settings->api_key) {
            return response()->stream(function () {
                echo "data: " . json_encode(['success' => false, 'error' => 'API key not configured']) . "\n\n";
            }, 200, ['Content-Type' => 'text/event-stream']);
        }

        if (!$this->checkLimits($sessionId)) {
            return response()->stream(function () {
                echo "data: " . json_encode(['success' => false, 'error' => 'Monthly limit reached']) . "\n\n";
            }, 200, ['Content-Type' => 'text/event-stream']);
        }

        $conversation = $this->getOrCreateConversation($sessionId, $ipAddress, $userAgent, $deviceType);

        if ($conversation->messages_count >= $this->settings->max_messages_conversation) {
            return response()->stream(function () {
                echo "data: " . json_encode(['success' => false, 'error' => 'Conversation limit reached']) . "\n\n";
            }, 200, ['Content-Type' => 'text/event-stream']);
        }

        $allPresets = $this->settings->getAllActivePresets();
        $allContextIds = [];
        foreach ($allPresets as $preset) {
            if (!empty($preset->context_ids)) {
                $allContextIds = array_merge($allContextIds, $preset->context_ids);
            }
        }

        if (!empty($allContextIds)) {
            $contextChunks = $this->vectorStore->searchSimilarInContexts(
                $userMessage,
                array_unique($allContextIds),
                $this->settings->getRagMaxResults(),
                $this->settings->getRagMinSimilarity()
            );
        } else {
            $contextChunks = $this->vectorStore->searchSimilar(
                $userMessage,
                $this->settings->getRagMaxResults(),
                $this->settings->getRagMinSimilarity()
            );
        }

        $conversationHistory = $this->getConversationHistory($conversation, 10);
        $systemPrompt = $this->buildSystemPrompt($contextChunks);
        $messages = $this->buildMessages($systemPrompt, $conversationHistory, $userMessage);

        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
            'tokens_used' => $this->estimateTokens($userMessage),
        ]);

        return response()->stream(function () use ($messages, $conversation, $contextChunks) {
            $fullContent = '';
            $totalTokens = 0;
            $startTime = microtime(true);

            $temperature = 0.7;
            $preset = $this->settings->preset;
            if ($preset && isset($preset->configuration['temperature'])) {
                $temperature = (float) $preset->configuration['temperature'];
            }

            while (ob_get_level() > 0) {
                ob_end_flush();
            }
            ob_implicit_flush(true);

            $client = new \GuzzleHttp\Client();

            try {
                Log::info('AI Chat Request', [
                    'listing_id' => $this->settings->listing_id,
                    'model' => $this->settings->model,
                    'api_key_prefix' => $this->settings->api_key ? substr($this->settings->api_key, 0, 10) . '...' : 'EMPTY',
                    'message_count' => count($messages),
                    'system_prompt_length' => strlen($messages[0]['content'] ?? ''),
                ]);

                $response = $client->request('POST', 'https://api.openai.com/v1/chat/completions', [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $this->settings->api_key,
                        'Content-Type' => 'application/json',
                    ],
                    'body' => json_encode([
                        'model' => $this->settings->model,
                        'messages' => $messages,
                        'max_tokens' => $this->settings->max_tokens_response,
                        'temperature' => $temperature,
                        'stream' => true,
                    ]),
                    ['stream' => true],
                ]);

                $body = $response->getBody();

                Log::info('AI Chat Response Status', [
                    'status' => $response->getStatusCode(),
                ]);

                $buffer = '';
                while (!$body->eof()) {
                    $chunk = $body->read(4096);
                    $buffer .= $chunk;

                    $lines = explode("\n", $buffer);
                    $buffer = array_pop($lines);

                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (empty($line)) continue;

                        if (str_starts_with($line, 'data: ')) {
                            $dataStr = substr($line, 6);

                            if ($dataStr === '[DONE]') {
                                break 2;
                            }

                            $data = json_decode($dataStr, true);
                            if (json_last_error() === JSON_ERROR_NONE && isset($data['choices'][0]['delta']['content'])) {
                                $token = $data['choices'][0]['delta']['content'];
                                $fullContent .= $token;
                                echo "data: " . json_encode(['type' => 'token', 'content' => $token]) . "\n\n";
                                @ob_flush();
                                flush();
                            }
                            if (isset($data['usage'])) {
                                $totalTokens = $data['usage']['total_tokens'] ?? 0;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::error('AI Stream error', [
                    'listing_id' => $this->settings->listing_id,
                    'error' => $e->getMessage(),
                ]);
                echo "data: " . json_encode(['type' => 'error', 'error' => $e->getMessage()]) . "\n\n";
            }

            AiMessage::create([
                'conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $fullContent,
                'tokens_used' => $totalTokens,
            ]);

            $conversation->incrementMessagesCount();

            $latencyMs = (int) ((microtime(true) - $startTime) * 1000);
            ChatbotAnalytics::incrementStats($this->settings->listing_id, 2, $totalTokens, false, $latencyMs);
            ChatbotTopQuestion::trackQuestion($this->settings->listing_id, $userMessage);

            $sources = [];
            if (!empty($contextChunks) && ($this->settings->show_citations ?? true)) {
                foreach ($contextChunks as $chunk) {
                    $sources[] = [
                        'type' => $chunk['source_type'],
                        'text' => mb_substr($chunk['chunk_text'], 0, 150) . (mb_strlen($chunk['chunk_text']) > 150 ? '...' : ''),
                        'similarity' => $chunk['similarity'],
                    ];
                }
            }

            $intentCta = null;
        if ($this->settings->intent_cta) {
            $intentCta = is_string($this->settings->intent_cta)
                ? json_decode($this->settings->intent_cta, true)
                : $this->settings->intent_cta;
        }

        $ctaSettings = null;
        if ($this->settings->cta_enabled) {
            $ctaSettings = [
                'enabled' => true,
                'intent_cta' => $intentCta,
            ];
        }

        echo "data: " . json_encode([
            'type' => 'done',
            'content' => $fullContent,
            'tokens' => $totalTokens,
            'sources' => $sources,
            'expandable_responses' => $this->settings->expandable_responses ?? true,
            'show_citations' => $this->settings->show_citations ?? true,
            'intent_cta' => $intentCta,
            'cta_settings' => $ctaSettings,
        ]) . "\n\n";

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
