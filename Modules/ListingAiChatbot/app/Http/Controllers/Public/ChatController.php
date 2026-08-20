<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\ListingAiChatbot\Models\ListingAiSetting;
use Modules\ListingAiChatbot\Services\AiChatbotService;

class ChatController extends Controller
{
    public function chat(Request $request, $slug)
    {
        $business = \Modules\Listings\Models\Listing::where('slug', $slug)->firstOrFail();

        $settings = ListingAiSetting::where('listing_id', $business->id)->first();

        if (!$settings || !$settings->is_enabled) {
            return response()->json([
                'success' => false,
                'error' => 'Chatbot not available',
                'message' => null,
            ], 404);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $moderated = $this->moderateInput($data['message']);
        if (!$moderated['allowed']) {
            return response()->json([
                'success' => false,
                'error' => 'Content not allowed',
                'message' => $moderated['message'],
            ], 400);
        }

        $sessionId = $data['session_id'] ?? $request->session()->getId() ?? Str::uuid()->toString();
        $deviceType = $this->detectDeviceType($request->userAgent());

        try {
            $chatbotService = new AiChatbotService($settings);
            $result = $chatbotService->chat(
                $moderated['message'],
                $sessionId,
                $request->ip(),
                $request->userAgent(),
                $deviceType
            );

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Service error',
                'message' => 'Disculpa, estoy teniendo problemas para responder. Por favor, intenta de nuevo.',
            ], 500);
        }
    }

    public function streamChat(Request $request, $slug)
    {
        $business = \Modules\Listings\Models\Listing::where('slug', $slug)->firstOrFail();

        $settings = ListingAiSetting::where('listing_id', $business->id)->first();

        if (!$settings || !$settings->is_enabled) {
            return response()->stream(function () {
                echo "data: " . json_encode(['success' => false, 'error' => 'Chatbot not available']) . "\n\n";
            }, 404, ['Content-Type' => 'text/event-stream']);
        }

        $data = $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:100',
        ]);

        $moderated = $this->moderateInput($data['message']);
        if (!$moderated['allowed']) {
            return response()->stream(function () use ($moderated) {
                echo "data: " . json_encode(['success' => false, 'error' => 'Content not allowed', 'message' => $moderated['message']]) . "\n\n";
            }, 400, ['Content-Type' => 'text/event-stream']);
        }

        $sessionId = $data['session_id'] ?? $request->session()->getId() ?? Str::uuid()->toString();
        $deviceType = $this->detectDeviceType($request->userAgent());

        try {
            $chatbotService = new AiChatbotService($settings);
            return $chatbotService->streamChat(
                $moderated['message'],
                $sessionId,
                $request->ip(),
                $request->userAgent(),
                $deviceType
            );
        } catch (\Exception $e) {
            return response()->stream(function () use ($e) {
                echo "data: " . json_encode(['success' => false, 'error' => $e->getMessage()]) . "\n\n";
            }, 500, ['Content-Type' => 'text/event-stream']);
        }
    }

    private function moderateInput(string $message): array
    {
        $cleaned = trim($message);

        if (strlen($cleaned) < 2) {
            return [
                'allowed' => false,
                'message' => 'El mensaje es muy corto.',
            ];
        }

        $suspiciousPatterns = [
            '/(http|https):\/\/[^\s]+\.(xyz|tk|ml|ga|cf|gq)/i',
            '/[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/i',
            '/\b(viagra|cialis|casino|lottery|porn|xxx)\b/i',
            '/\b(free money|bitcoin giveaway|you won)\b/i',
            '/(.)\1{5,}/',
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (preg_match($pattern, $cleaned)) {
                return [
                    'allowed' => false,
                    'message' => 'Este mensaje no puede ser procesado.',
                ];
            }
        }

        $cleaned = preg_replace('/[^\p{L}\p{N}\p{P}\p{Zs}\p{Pc}\p{Pd}\p{Ps}\p{Pe}]/u', ' ', $cleaned);
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        $cleaned = trim($cleaned);

        return [
            'allowed' => true,
            'message' => $cleaned,
        ];
    }

    private function detectDeviceType(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }

        $userAgent = strtolower($userAgent);

        if (str_contains($userAgent, 'mobile') ||
            str_contains($userAgent, 'android') ||
            str_contains($userAgent, 'iphone') ||
            str_contains($userAgent, 'ipad') ||
            str_contains($userAgent, 'tablet')) {
            if (str_contains($userAgent, 'ipad') || str_contains($userAgent, 'tablet')) {
                return 'tablet';
            }
            return 'mobile';
        }

        return 'desktop';
    }

    public function getSettings(Request $request, $slug)
    {
        $business = \Modules\Listings\Models\Listing::where('slug', $slug)->firstOrFail();

        $settings = ListingAiSetting::where('listing_id', $business->id)->first();

        if (!$settings || !$settings->is_enabled) {
            return response()->json([
                'available' => false,
            ]);
        }

        $initialSuggestions = [];
        $allPresets = $settings->getAllActivePresets();
        if ($allPresets->isNotEmpty()) {
            $mainSuggestions = $allPresets->first()->initial_suggestions ?? [];
            if (is_string($mainSuggestions)) {
                $mainSuggestions = json_decode($mainSuggestions, true) ?? [];
            }
            $initialSuggestions = is_array($mainSuggestions) ? $mainSuggestions : [];

            if ($allPresets->count() > 1) {
                foreach ($allPresets->skip(1) as $preset) {
                    $additional = $preset->initial_suggestions ?? [];
                    if (is_string($additional)) {
                        $additional = json_decode($additional, true) ?? [];
                    }
                    if (is_array($additional)) {
                        $initialSuggestions = array_merge($initialSuggestions, $additional);
                    }
                }
            }
        }

        return response()->json([
            'available' => true,
            'business_name' => $business->name,
            'chatbot_name' => $settings->chatbot_name ?: 'Asistente Virtual',
            'chatbot_avatar' => $settings->chatbot_avatar,
            'widget_color' => $settings->widget_color,
            'widget_theme' => $settings->widget_theme ?? 'light',
            'initial_suggestions' => $initialSuggestions,
            'expandable_responses' => $settings->expandable_responses ?? true,
            'show_citations' => $settings->show_citations ?? true,
            'cta_settings' => $settings->cta_enabled ? [
                'enabled' => $settings->cta_enabled,
                'primary_text' => $settings->cta_primary_text,
                'primary_url' => $settings->cta_primary_url,
                'secondary_text' => $settings->cta_secondary_text,
                'secondary_url' => $settings->cta_secondary_url,
                'intent_cta' => $settings->intent_cta,
            ] : null,
            'lead_capture' => $settings->lead_capture_enabled ? [
                'enabled' => true,
                'trigger' => $settings->lead_capture_trigger ?? 'after_3_messages',
                'title' => $settings->lead_capture_title ?? '¿Te gustaría recibir noticias sobre nosotros?',
                'description' => $settings->lead_capture_description ?? 'Déjanos tu correo y te mantendremos informado.',
            ] : null,
        ]);
    }

    public function conversation(Request $request, $slug)
    {
        $business = \Modules\Listings\Models\Listing::where('slug', $slug)->firstOrFail();

        $settings = ListingAiSetting::where('listing_id', $business->id)->first();

        if (!$settings || !$settings->is_enabled) {
            return response()->json([
                'success' => false,
                'messages' => [],
            ]);
        }

        $sessionId = $request->get('session_id') ?? $request->session()->getId();

        $conversation = \Modules\ListingAiChatbot\Models\AiConversation::where('listing_id', $business->id)
            ->where('session_id', $sessionId)
            ->with('messages')
            ->first();

        if (!$conversation) {
            return response()->json([
                'success' => true,
                'messages' => [],
            ]);
        }

        $messages = $conversation->messages->map(fn($msg) => [
            'role' => $msg->role,
            'content' => $msg->content,
            'created_at' => $msg->created_at->toIso8601String(),
        ]);

        return response()->json([
            'success' => true,
            'messages' => $messages,
        ]);
    }

    public function captureLead(Request $request, $slug)
    {
        $business = \Modules\Listings\Models\Listing::where('slug', $slug)->firstOrFail();

        $settings = ListingAiSetting::where('listing_id', $business->id)->first();

        if (!$settings || !$settings->is_enabled || !$settings->lead_capture_enabled) {
            return response()->json(['success' => false, 'error' => 'Lead capture not available'], 400);
        }

        $data = $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
            'session_id' => 'nullable|string|max:100',
        ]);

        try {
            $lead = \Modules\ListingLeads\Models\ListingLead::create([
                'listing_id' => $business->id,
                'name' => $data['name'] ?? 'Chatbot Visitor',
                'email' => $data['email'],
                'status' => 'new',
                'source' => 'chatbot',
                'ip_address' => $request->ip(),
                'metadata' => [
                    'session_id' => $data['session_id'] ?? null,
                    'captured_at' => now()->toIso8601String(),
                ],
            ]);

            return response()->json(['success' => true, 'lead_id' => $lead->id]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => 'Could not save lead'], 500);
        }
    }
}
