<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\ListingAiChatbot\Models\ChatbotWidget;
use Modules\ListingAiChatbot\Models\ChatbotWidgetAnalytics;
use Modules\ListingAiChatbot\Services\WidgetChatService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\StreamedResponse;

class WidgetApiController extends Controller
{
    public function settings(string $publicKey, Request $request): JsonResponse
    {
        $widget = ChatbotWidget::wherePublicKey($publicKey)->first();

        if (!$widget) {
            return response()->json(['error' => 'Widget no encontrado'], 404);
        }

        if (!$widget->is_enabled) {
            return response()->json(['error' => 'Widget deshabilitado'], 403);
        }

        $domain = $this->getDomainFromRequest($request);

        if (!$widget->isDomainAllowed($domain)) {
            return response()->json(['error' => 'Widget no disponible para este dominio'], 403);
        }

        $settings = $widget->getAiSetting();

        ChatbotWidgetAnalytics::track($publicKey, 'load', ['domain' => $domain]);

        return response()->json([
            'chatbot_name' => $settings->chatbot_name ?: 'Asistente Virtual',
            'chatbot_avatar' => $settings->chatbot_avatar,
            'widget_color' => $settings->widget_color ?: '#3B82F6',
            'widget_theme' => $settings->widget_theme ?? 'light',
            'initial_suggestions' => $this->parseInitialSuggestions($settings),
            'cta_enabled' => $settings->cta_enabled ?? false,
            'intent_cta' => $this->parseIntentCta($settings),
            'intent_buttons' => $this->buildIntentButtons($settings),
        ]);
    }

    public function chat(string $publicKey, Request $request): StreamedResponse
    {
        $widget = ChatbotWidget::wherePublicKey($publicKey)->first();

        if (!$widget) {
            return $this->errorStream('Widget no encontrado');
        }

        if (!$widget->is_enabled) {
            return $this->errorStream('Widget deshabilitado');
        }

        $domain = $this->getDomainFromRequest($request);

        if (!$widget->isDomainAllowed($domain)) {
            return $this->errorStream('Dominio no autorizado');
        }

        $validated = $request->validate([
            'message' => 'required|string|max:2000',
            'session_id' => 'nullable|string|max:100',
            'intent' => 'nullable|string|max:50',
        ]);

        ChatbotWidgetAnalytics::track($publicKey, 'message', ['domain' => $domain, 'intent' => $validated['intent'] ?? null]);

        $chatService = new WidgetChatService($widget->listing);

        return $chatService->streamChat(
            $validated['message'],
            $validated['session_id'] ?? session()->getId() ?? uniqid(),
            $request->ip(),
            $request->userAgent(),
            $validated['intent'] ?? null
        );
    }

    public function event(string $publicKey, Request $request): JsonResponse
    {
        $widget = ChatbotWidget::wherePublicKey($publicKey)->first();

        if (!$widget) {
            return response()->json(['error' => 'Widget no encontrado'], 404);
        }

        $eventType = $request->input('event_type');
        $metadata = $request->input('metadata', []);
        $domain = $this->getDomainFromRequest($request);

        $metadata['domain'] = $domain;

        ChatbotWidgetAnalytics::track($publicKey, $eventType, $metadata);

        return response()->json(['success' => true]);
    }

    private function getDomainFromRequest(Request $request): ?string
    {
        $referer = $request->headers->get('Referer');
        if ($referer) {
            return parse_url($referer, PHP_URL_HOST);
        }

        return $request->input('domain');
    }

    private function parseInitialSuggestions($settings): array
    {
        if (!$settings || !$settings->preset) {
            return [];
        }

        $suggestions = $settings->preset->initial_suggestions;

        if (is_array($suggestions)) {
            return $suggestions;
        }

        if (is_string($suggestions)) {
            $decoded = json_decode($suggestions, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function parseIntentCta($settings): ?array
    {
        if (!$settings || !$settings->intent_cta) {
            return null;
        }

        $intentCta = $settings->intent_cta;

        if (is_array($intentCta)) {
            return $intentCta;
        }

        if (is_string($intentCta)) {
            return json_decode($intentCta, true);
        }

        return null;
    }

    private function buildIntentButtons($settings): array
    {
        $intentCta = $this->parseIntentCta($settings);
        if (!$intentCta) {
            return [];
        }

        $buttons = [];
        $icons = [
            'appointment' => 'calendar',
            'purchase' => 'bag',
            'contact' => 'telephone',
            'support' => 'question-circle',
        ];

        $intentOrder = ['appointment', 'purchase', 'contact', 'support'];

        foreach ($intentOrder as $key) {
            if (isset($intentCta[$key]['enabled']) && $intentCta[$key]['enabled']) {
                $buttons[] = [
                    'key' => $key,
                    'text' => $intentCta[$key]['text'] ?: $this->getDefaultIntentText($key),
                    'icon' => $icons[$key] ?? 'chat',
                ];
            }
        }

        return $buttons;
    }

    private function getDefaultIntentText(string $key): string
    {
        return match ($key) {
            'appointment' => 'Agendar cita',
            'purchase' => 'Ver precios',
            'contact' => 'Contactar',
            'support' => 'Obtener ayuda',
            default => '',
        };
    }

    private function errorStream(string $message): StreamedResponse
    {
        return response()->stream(function () use ($message) {
            echo "data: " . json_encode(['error' => $message]) . "\n\n";
        }, 403, ['Content-Type' => 'text/event-stream']);
    }
}
