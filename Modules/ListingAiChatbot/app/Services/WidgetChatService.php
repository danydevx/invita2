<?php

namespace Modules\ListingAiChatbot\Services;

use Modules\ListingAiChatbot\Models\ListingAiSetting;
use Modules\ListingAiChatbot\Models\AiConversation;
use Modules\ListingAiChatbot\Models\AiMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WidgetChatService
{
    private $settings;
    private VectorStoreService $vectorStore;

    public function __construct($business)
    {
        $this->settings = $business->aiSetting;
        $this->vectorStore = new VectorStoreService($this->settings);
    }

    public function streamChat(string $userMessage, string $sessionId, ?string $ipAddress = null, ?string $userAgent = null, ?string $intent = null): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        if (!$this->settings || !$this->settings->is_enabled) {
            return $this->errorResponse('Chatbot no disponible');
        }

        if (!$this->settings->api_key) {
            return $this->errorResponse('API no configurada');
        }

        $conversation = $this->getOrCreateConversation($sessionId, $ipAddress, $userAgent);

        $contextChunks = $this->vectorStore->searchSimilar(
            $userMessage,
            $this->settings->getRagMaxResults(),
            $this->settings->getRagMinSimilarity()
        );

        $conversationHistory = $this->getConversationHistory($conversation, 10);
        $systemPrompt = $this->buildSystemPrompt($contextChunks, $intent);
        $messages = $this->buildMessages($systemPrompt, $conversationHistory, $userMessage);

        AiMessage::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userMessage,
            'tokens_used' => $this->estimateTokens($userMessage),
        ]);

        return response()->stream(function () use ($messages, $conversation, $contextChunks, $userMessage, $intent) {
            $fullContent = '';
            $totalTokens = 0;
            $startTime = microtime(true);

            $temperature = 0.7;
            $preset = $this->settings->preset;
            if ($preset && isset($preset->configuration['temperature'])) {
                $temperature = (float) $preset->configuration['temperature'];
            }

            $client = new \GuzzleHttp\Client();

            try {
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
                Log::error('Widget AI Stream error', [
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

            $showCta = false;
            if (stripos($fullContent, 'show_cta:true') !== false) {
                $showCta = true;
                $fullContent = str_ireplace('show_cta:true', '', $fullContent);
                $fullContent = trim($fullContent);
            }

            $activeIntentCta = null;
            if ($showCta && $intent && $this->settings->intent_cta) {
                $intentCta = is_string($this->settings->intent_cta)
                    ? json_decode($this->settings->intent_cta, true)
                    : $this->settings->intent_cta;
                $activeIntentCta = $intentCta[$intent] ?? null;
            }

            echo "data: " . json_encode([
                'type' => 'done',
                'content' => $fullContent,
                'tokens' => $totalTokens,
                'sources' => $sources,
                'expandable_responses' => $this->settings->expandable_responses ?? true,
                'show_citations' => $this->settings->show_citations ?? true,
                'intent_cta' => $activeIntentCta,
                'show_cta' => $showCta,
            ]) . "\n\n";

        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'Connection' => 'keep-alive',
        ]);
    }

    private function getOrCreateConversation(string $sessionId, ?string $ipAddress, ?string $userAgent): AiConversation
    {
        $conversation = AiConversation::where('listing_id', $this->settings->listing_id)
            ->where('session_id', $sessionId)
            ->first();

        if (!$conversation) {
            $conversation = AiConversation::create([
                'listing_id' => $this->settings->listing_id,
                'session_id' => $sessionId,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'device_type' => 'widget',
                'country' => 'Unknown',
                'city' => 'Unknown',
                'country_code' => 'XX',
                'messages_count' => 0,
                'started_at' => now(),
                'last_activity_at' => now(),
            ]);
        }

        return $conversation;
    }

    private function getConversationHistory(AiConversation $conversation, int $limit = 10): array
    {
        $messages = AiMessage::where('conversation_id', $conversation->id)
            ->orderBy('created_at', 'desc')
            ->limit($limit * 2)
            ->get()
            ->reverse()
            ->values();

        $history = [];
        foreach ($messages as $msg) {
            $history[] = [
                'role' => $msg->role,
                'content' => $msg->content,
            ];
        }

        return $history;
    }

    private function buildSystemPrompt(array $contextChunks, ?string $intent = null): string
    {
        $listing = $this->settings->listing;
        $preset = $this->settings->preset;

        if ($preset && $preset->system_prompt_template) {
            $systemPrompt = $preset->system_prompt_template;
            $systemPrompt = str_replace('{business_name}', $listing->name ?? 'este negocio', $systemPrompt);
            $systemPrompt = str_replace('{greeting_addition}', '', $systemPrompt);
        } else {
            $systemPrompt = $this->settings->system_prompt ?: $this->settings->getDefaultSystemPrompt();
            $systemPrompt = str_replace('{business_name}', $listing->name ?? 'este negocio', $systemPrompt);
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

        if ($intent) {
            $systemPrompt = $this->addIntentInstructions($systemPrompt, $intent);
        }

        return $systemPrompt;
    }

    private function addIntentInstructions(string $basePrompt, string $intent): string
    {
        $intentInstructions = match ($intent) {
            'appointment' => "\n\n[MODO ASISTENTE DE AGENDADO] Has iniciado una conversación para agendar una cita. Tu objetivo es recopilar la siguiente información: 1) ¿Qué servicio o tipo de cita necesita? 2) ¿Qué día prefiere? 3) ¿Qué hora está disponible? Haz UNA pregunta a la vez para no abrumar al usuario. Cuando tengas suficiente información (día, hora y servicio), indica que el usuario puede agendar. Para indicar que debe mostrarse el botón CTA, incluye la palabra 'show_cta:true' en tu respuesta (en una línea separada, no la escondas en el texto).",
            'purchase' => "\n\n[MODO ASISTENTE DE VENTAS] Has iniciado una conversación sobre productos o precios. Tu objetivo es entender qué busca el usuario: 1) ¿Qué tipo de producto le interesa? 2) ¿Cuál es su presupuesto? 3) ¿Hay algo específico que busca? Haz UNA pregunta a la vez. Cuando tengas suficiente información para recomendar productos, indica que puede ver los productos. Para indicar que debe mostrarse el botón CTA, incluye la palabra 'show_cta:true' en tu respuesta (en una línea separada).",
            'contact' => "\n\n[MODO ASISTENTE DE CONTACTO] Has iniciado una conversación para contactar al negocio. Tu objetivo es entender cómo puede comunicarse el usuario: 1) ¿Prefiere llamada, mensaje o visita? 2) ¿Cuál es su tema? Proporciona la información de contacto relevante (teléfono, email, dirección). Haz preguntas para entender mejor la necesidad. Cuando tengas suficiente información, indica las opciones de contacto disponibles. Para indicar que debe mostrarse el botón CTA, incluye la palabra 'show_cta:true' en tu respuesta (en una línea separada).",
            'support' => "\n\n[MODO ASISTENTE DE SOPORTE] Has iniciado una conversación de soporte técnico. Tu objetivo es ayudar a resolver el problema del usuario: 1) ¿Cuál es el problema? 2) ¿Cuándo ocurrió? 3) ¿Ha intentado algo? Escucha activamente y haz preguntas de seguimiento. Cuando tengas suficiente información para proporcionar una solución o dirección, indica los próximos pasos. Para indicar que debe mostrarse el botón CTA, incluye la palabra 'show_cta:true' en tu respuesta (en una línea separada).",
            default => '',
        };

        return $basePrompt . $intentInstructions;
    }

    private function buildMessages(string $systemPrompt, array $conversationHistory, string $userMessage): array
    {
        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
        ];

        foreach ($conversationHistory as $historyMsg) {
            $messages[] = $historyMsg;
        }

        $messages[] = [
            'role' => 'user',
            'content' => $userMessage,
        ];

        return $messages;
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
            'medium' => "Responde de manera BALANCEADA. No demasiado corto ni demasiado largo. Mantén un párrafo.",
            'long' => "Responde de manera DETALLADA y completa. Desarrolla tu respuesta con profundidad.",
            default => "Responde de manera natural y útil.",
        };
    }

    private function estimateTokens(string $text): int
    {
        return (int) (strlen($text) / 4);
    }

    private function errorResponse(string $message): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        return response()->stream(function () use ($message) {
            echo "data: " . json_encode(['success' => false, 'error' => $message]) . "\n\n";
        }, 200, ['Content-Type' => 'text/event-stream']);
    }
}
