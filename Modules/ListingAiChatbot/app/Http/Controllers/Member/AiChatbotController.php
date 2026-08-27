<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Modules\ListingAiChatbot\Models\ListingAiSetting;
use Modules\ListingAiChatbot\Models\AiContext;
use Modules\ListingAiChatbot\Models\ChatbotPreset;
use Modules\ListingAiChatbot\Services\AiChatbotService;
use Modules\ListingAiChatbot\Services\UrlContentExtractor;

class AiChatbotController extends Controller
{
    public function index(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $settings = ListingAiSetting::where('listing_id', $business->id)->first();

        $presets = ChatbotPreset::getActivePresets($business->id);

        $contexts = AiContext::where('listing_id', $business->id)
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'content', 'is_active', 'created_at']);

        $embeddingCounts = [];
        $counts = [
            'product' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'product')->count(),
            'service' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'service')->count(),
            'promotion' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'promotion')->count(),
            'faq' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'faq')->count(),
            'location' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'location')->count(),
            'about' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'about')->count(),
            'custom' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'custom')->count(),
            'restaurant_category' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'restaurant_category')->count(),
            'restaurant_product' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'restaurant_product')->count(),
            'social_network' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'social_network')->count(),
            'appointment' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'appointment')->count(),
            'appointment_exception' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id)->where('source_type', 'appointment_exception')->count(),
        ];
        $embeddingCounts = $counts;

        return Inertia::render('Member/AiChatbot/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
            ],
            'settings' => $settings ? [
                'id' => $settings->id,
                'preset_id' => $settings->preset_id,
                'additional_preset_ids' => $settings->additional_preset_ids ?? [],
                'provider' => $settings->provider,
                'api_key' => $settings->api_key ? '********' : '',
                'model' => $settings->model,
                'embedding_model' => $settings->embedding_model,
                'system_prompt' => $settings->system_prompt,
                'chatbot_name' => $settings->chatbot_name,
                'chatbot_avatar' => $settings->chatbot_avatar,
                'personality' => $settings->personality ?? 'friendly',
                'response_length' => $settings->response_length ?? 'medium',
                'expandable_responses' => $settings->expandable_responses ?? true,
                'show_citations' => $settings->show_citations ?? true,
                'max_conversations_month' => $settings->max_conversations_month,
                'max_messages_conversation' => $settings->max_messages_conversation,
                'max_tokens_response' => $settings->max_tokens_response,
                'widget_color' => $settings->widget_color,
                'widget_theme' => $settings->widget_theme ?? 'light',
                'is_enabled' => $settings->is_enabled,
                'allow_reset_chat' => $settings->allow_reset_chat ?? false,
                'url_import_max_chars' => $settings->url_import_max_chars ?? 5000,
                'rag_min_similarity' => $settings->rag_min_similarity ?? 0.250,
                'rag_max_results' => $settings->rag_max_results ?? 5,
                'cta_settings' => [
                    'enabled' => $settings->cta_enabled ?? false,
                    'primary_text' => $settings->cta_primary_text ?? '',
                    'primary_url' => $settings->cta_primary_url ?? '',
                    'secondary_text' => $settings->cta_secondary_text ?? '',
                    'secondary_url' => $settings->cta_secondary_url ?? '',
                    'intent_cta' => $settings->intent_cta,
                ],
                'lead_capture_enabled' => $settings->lead_capture_enabled ?? false,
                'lead_capture_title' => $settings->lead_capture_title ?? '¿Te gustaría recibir noticias sobre nosotros?',
                'lead_capture_description' => $settings->lead_capture_description ?? 'Déjanos tu correo y te mantendremos informado.',
            ] : null,
            'presets' => $presets,
            'contexts' => $contexts,
            'embeddingCounts' => $embeddingCounts,
        ]);
    }

    public function saveSettings(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $data = $request->validate([
            'provider' => 'nullable|in:openai,minimax',
            'api_key' => 'nullable|string|max:500',
            'model' => 'nullable|string|max:100',
            'embedding_model' => 'nullable|string|max:100',
            'system_prompt' => 'nullable|string',
            'chatbot_name' => 'nullable|string|max:100',
            'chatbot_avatar' => 'nullable|image|max:1024|mimes:jpg,jpeg,png',
            'preset_id' => 'nullable|integer|exists:chatbot_presets,id',
            'additional_preset_ids' => 'nullable|array',
            'additional_preset_ids.*' => 'integer|exists:chatbot_presets,id',
            'personality' => 'nullable|in:professional,friendly,formal,casual',
            'response_length' => 'nullable|in:short,medium,long',
            'expandable_responses' => 'boolean',
            'show_citations' => 'boolean',
            'max_conversations_month' => 'nullable|integer|min:1|max:10000',
            'max_messages_conversation' => 'nullable|integer|min:1|max:500',
            'max_tokens_response' => 'nullable|integer|min:100|max:4000',
            'widget_color' => 'nullable|string|regex:/^#[0-9A-Fa-f]{6}$/',
            'widget_theme' => 'nullable|in:light,dark',
            'is_enabled' => 'boolean',
            'allow_reset_chat' => 'boolean',
            'url_import_max_chars' => 'nullable|integer|min:100|max:50000',
            'rag_min_similarity' => 'nullable|numeric|min:0|max:1',
            'rag_max_results' => 'nullable|integer|min:1|max:20',
            'cta_settings' => 'nullable|string',
            'lead_capture_settings' => 'nullable|string',
        ]);

        $updateData = [
            'provider' => $data['provider'] ?? 'openai',
            'model' => $data['model'] ?? 'gpt-4o-mini',
            'embedding_model' => $data['embedding_model'] ?? 'text-embedding-3-small',
            'system_prompt' => $data['system_prompt'] ?? null,
            'chatbot_name' => $data['chatbot_name'] ?? null,
            'preset_id' => $data['preset_id'] ?? null,
            'additional_preset_ids' => $data['additional_preset_ids'] ?? [],
            'personality' => $data['personality'] ?? 'friendly',
            'response_length' => $data['response_length'] ?? 'medium',
            'expandable_responses' => $data['expandable_responses'] ?? true,
            'show_citations' => $data['show_citations'] ?? true,
            'max_conversations_month' => $data['max_conversations_month'] ?? 500,
            'max_messages_conversation' => $data['max_messages_conversation'] ?? 50,
            'max_tokens_response' => $data['max_tokens_response'] ?? 500,
            'widget_color' => $data['widget_color'] ?? '#3B82F6',
            'widget_theme' => $data['widget_theme'] ?? 'light',
            'is_enabled' => $data['is_enabled'] ?? false,
            'allow_reset_chat' => $data['allow_reset_chat'] ?? false,
            'url_import_max_chars' => $data['url_import_max_chars'] ?? 5000,
            'rag_min_similarity' => (float) ($data['rag_min_similarity'] ?? 0.25),
            'rag_max_results' => (int) ($data['rag_max_results'] ?? 5),
        ];

        if (!empty($data['cta_settings'])) {
            $ctaSettings = json_decode($data['cta_settings'], true);
            $updateData['cta_enabled'] = $ctaSettings['enabled'] ?? false;
            $updateData['cta_primary_text'] = $ctaSettings['primary_text'] ?? '';
            $updateData['cta_primary_url'] = $ctaSettings['primary_url'] ?? '';
            $updateData['cta_secondary_text'] = $ctaSettings['secondary_text'] ?? '';
            $updateData['cta_secondary_url'] = $ctaSettings['secondary_url'] ?? '';
            $updateData['intent_cta'] = isset($ctaSettings['intent_cta']) ? json_encode($ctaSettings['intent_cta']) : null;
        }

        if (!empty($data['lead_capture_settings'])) {
            $lcSettings = json_decode($data['lead_capture_settings'], true);
            $updateData['lead_capture_enabled'] = $lcSettings['enabled'] ?? false;
            $updateData['lead_capture_title'] = $lcSettings['title'] ?? '¿Te gustaría recibir noticias sobre nosotros?';
            $updateData['lead_capture_description'] = $lcSettings['description'] ?? 'Déjanos tu correo y te mantendremos informado.';
        }

        if ($request->hasFile('chatbot_avatar')) {
            $file = $request->file('chatbot_avatar');
            $path = $file->store('chatbot-avatars', 'public');
            $updateData['chatbot_avatar'] = '/storage/' . $path;
        } else {
            unset($updateData['chatbot_avatar']);
        }

        if (!empty($data['api_key']) && $data['api_key'] !== '********') {
            $updateData['api_key'] = $data['api_key'];
        }

        $settings = ListingAiSetting::updateOrCreate(
            ['listing_id' => $business->id],
            $updateData
        );

        return redirect()->back()->with('success', 'Configuración guardada correctamente.');
    }

    public function storeContext(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'content_for_editing' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $context = AiContext::create([
            'listing_id' => $business->id,
            'title' => $data['title'],
            'content' => $data['content'],
            'content_for_editing' => $data['content_for_editing'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Contexto creado correctamente.');
    }

    public function updateContext(Request $request, \Modules\Listings\Models\Listing $business, $contextId)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $context = AiContext::where('listing_id', $business->id)->findOrFail($contextId);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'content_for_editing' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $context->update([
            'title' => $data['title'],
            'content' => $data['content'],
            'content_for_editing' => $data['content_for_editing'] ?? null,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Contexto actualizado correctamente.');
    }

    public function destroyContext(Request $request, \Modules\Listings\Models\Listing $business, $contextId)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $context = AiContext::where('listing_id', $business->id)->findOrFail($contextId);

        $context->delete();

        return redirect()->back()->with('success', 'Contexto eliminado correctamente.');
    }

    public function reindex(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $settings = ListingAiSetting::where('listing_id', $business->id)->first();

        if (!$settings) {
            return redirect()->back()->with('error', 'Primero configura los ajustes del chatbot.');
        }

        if (!$settings->api_key) {
            return redirect()->back()->with('error', 'Primero ingresa la API key.');
        }

        try {
            $chatbotService = new AiChatbotService($settings);
            $stats = $chatbotService->reindexContent();

            return redirect()->back()->with('success', "Contenido reindexado: {$stats['products']} productos, {$stats['services']} servicios, {$stats['promotions']} promociones, {$stats['faqs']} FAQs, {$stats['locations']} ubicaciones, {$stats['about']} acerca de, {$stats['custom']} contextos personalizados.")->with('reindexStats', $stats);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al reindexar: ' . $e->getMessage());
        }
    }

    private function getEmbeddingCounts(int $businessId): array
    {
        return [
            'product' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'product')->count(),
            'service' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'service')->count(),
            'promotion' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'promotion')->count(),
            'faq' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'faq')->count(),
            'location' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'location')->count(),
            'about' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'about')->count(),
            'custom' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'custom')->count(),
            'restaurant_category' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'restaurant_category')->count(),
            'restaurant_product' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'restaurant_product')->count(),
            'social_network' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'social_network')->count(),
            'appointment' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'appointment')->count(),
            'appointment_exception' => \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $businessId)->where('source_type', 'appointment_exception')->count(),
        ];
    }

    public function extractUrl(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $data = $request->validate([
            'url' => 'required|url|max:2048',
        ]);

        $settings = ListingAiSetting::where('listing_id', $business->id)->first();
        $maxChars = $settings->url_import_max_chars ?? 5000;

        $extractor = new UrlContentExtractor();
        $result = $extractor->extract($data['url'], $maxChars);

        return redirect()->back()->with('extractResult', $result);
    }

    public function widgetSettings(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $widget = \Modules\ListingAiChatbot\Models\ChatbotWidget::where('listing_id', $business->id)->first();

        if (!$widget) {
            $widget = \Modules\ListingAiChatbot\Models\ChatbotWidget::generateForBusiness($business);
        }

        $stats = [
            'loads' => \Modules\ListingAiChatbot\Models\ChatbotWidgetAnalytics::where('public_key', $widget->public_key)->where('event_type', 'load')->count(),
            'messages' => \Modules\ListingAiChatbot\Models\ChatbotWidgetAnalytics::where('public_key', $widget->public_key)->where('event_type', 'message')->count(),
            'opens' => \Modules\ListingAiChatbot\Models\ChatbotWidgetAnalytics::where('public_key', $widget->public_key)->where('event_type', 'open')->count(),
            'cta_clicks' => \Modules\ListingAiChatbot\Models\ChatbotWidgetAnalytics::where('public_key', $widget->public_key)->where('event_type', 'cta_click')->count(),
        ];

        $aiSettings = $business->aiSetting;
        $intentCta = null;
        if ($aiSettings && $aiSettings->intent_cta) {
            $intentCta = is_string($aiSettings->intent_cta)
                ? json_decode($aiSettings->intent_cta, true)
                : $aiSettings->intent_cta;
        }

        return response()->json([
            'widget' => $widget,
            'stats' => $stats,
            'intent_cta' => $intentCta,
        ]);
    }

    public function saveWidgetSettings(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $validated = $request->validate([
            'is_enabled' => 'boolean',
            'allowed_domain' => 'nullable|string|max:255',
            'position' => 'in:right,left',
            'show_intent_buttons' => 'boolean',
            'intent_cta' => 'nullable|array',
        ]);

        $widget = \Modules\ListingAiChatbot\Models\ChatbotWidget::where('listing_id', $business->id)->first();

        if (!$widget) {
            $widget = \Modules\ListingAiChatbot\Models\ChatbotWidget::generateForBusiness($business);
        }

        $widget->update([
            'is_enabled' => $validated['is_enabled'] ?? false,
            'allowed_domain' => $validated['allowed_domain'] ?? null,
        ]);

        if (isset($validated['intent_cta'])) {
            $aiSettings = \Modules\ListingAiChatbot\Models\ListingAiSetting::where('listing_id', $business->id)->first();
            if (!$aiSettings) {
                $aiSettings = new \Modules\ListingAiChatbot\Models\ListingAiSetting(['listing_id' => $business->id]);
            }
            $aiSettings->intent_cta = $validated['intent_cta'];
            $aiSettings->cta_enabled = !empty(array_filter($validated['intent_cta'], fn($i) => $i['enabled'] ?? false));
            $aiSettings->save();
        }

        return redirect()->back()->with('success', 'Configuración del widget guardada.');
    }

    public function regenerateWidgetKey(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $widget = \Modules\ListingAiChatbot\Models\ChatbotWidget::where('listing_id', $business->id)->first();

        if (!$widget) {
            $widget = \Modules\ListingAiChatbot\Models\ChatbotWidget::generateForBusiness($business);
        }

        $widget->regeneratePublicKey();

        return response()->json([
            'success' => true,
            'widget' => $widget,
        ]);
    }

    public function historyJson(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        return response()->json([
            'conversations' => [],
        ]);
    }

    public function historyDetailJson(Request $request, \Modules\Listings\Models\Listing $business, $sessionId)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        return response()->json([
            'messages' => [],
        ]);
    }
}
