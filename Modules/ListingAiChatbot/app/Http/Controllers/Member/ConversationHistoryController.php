<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;
use Modules\ListingAiChatbot\Models\AiConversation;
use Modules\ListingAiChatbot\Models\AiMessage;

class ConversationHistoryController extends Controller
{
    public function index(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $conversations = AiConversation::where('listing_id', $business->id)
            ->orderBy('last_activity_at', 'desc')
            ->limit(100)
            ->get()
            ->map(fn($conv) => [
                'id' => $conv->id,
                'session_id' => $conv->session_id,
                'ip_address' => $conv->ip_address,
                'country' => $conv->country,
                'country_code' => $conv->country_code,
                'city' => $conv->city,
                'user_agent' => $conv->user_agent,
                'messages_count' => $conv->messages_count,
                'started_at' => $conv->started_at?->toIso8601String(),
                'last_activity_at' => $conv->last_activity_at?->toIso8601String(),
                'preview' => $this->getPreview($conv),
            ]);

        return Inertia::render('Member/AiChatbot/ConversationHistory', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'conversations' => $conversations,
        ]);
    }

    public function show(Request $request, \Modules\Listings\Models\Listing $business, $sessionId)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $conversation = AiConversation::where('listing_id', $business->id)
            ->where('session_id', $sessionId)
            ->with('messages')
            ->firstOrFail();

        $messages = $conversation->messages->map(fn($msg) => [
            'id' => $msg->id,
            'role' => $msg->role,
            'content' => $msg->content,
            'tokens_used' => $msg->tokens_used,
            'created_at' => $msg->created_at?->toIso8601String(),
        ]);

        return Inertia::render('Member/AiChatbot/ConversationDetail', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'conversation' => [
                'id' => $conversation->id,
                'session_id' => $conversation->session_id,
                'ip_address' => $conversation->ip_address,
                'country' => $conversation->country,
                'country_code' => $conversation->country_code,
                'city' => $conversation->city,
                'user_agent' => $conversation->user_agent,
                'messages_count' => $conversation->messages_count,
                'started_at' => $conversation->started_at?->toIso8601String(),
                'last_activity_at' => $conversation->last_activity_at?->toIso8601String(),
            ],
            'messages' => $messages,
        ]);
    }

    private function getPreview(AiConversation $conversation): string
    {
        $lastMessage = $conversation->messages()->orderBy('created_at', 'desc')->first();
        if (!$lastMessage) {
            return '';
        }
        $content = $lastMessage->content;
        if (strlen($content) > 80) {
            $content = substr($content, 0, 80) . '...';
        }
        return $content;
    }

    public function indexJson(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $conversations = AiConversation::where('listing_id', $business->id)
            ->orderBy('last_activity_at', 'desc')
            ->limit(100)
            ->get()
            ->map(fn($conv) => [
                'id' => $conv->id,
                'session_id' => $conv->session_id,
                'ip_address' => $conv->ip_address,
                'country' => $conv->country,
                'country_code' => $conv->country_code,
                'city' => $conv->city,
                'user_agent' => $conv->user_agent,
                'device_type' => $conv->device_type,
                'messages_count' => $conv->messages_count,
                'started_at' => $conv->started_at?->toIso8601String(),
                'last_activity_at' => $conv->last_activity_at?->toIso8601String(),
                'preview' => $this->getPreview($conv),
            ]);

        return response()->json(['conversations' => $conversations]);
    }

    public function showJson(Request $request, \Modules\Listings\Models\Listing $business, $sessionId)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $conversation = AiConversation::where('listing_id', $business->id)
            ->where('session_id', $sessionId)
            ->with('messages')
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation not found'], 404);
        }

        $messages = $conversation->messages->map(fn($msg) => [
            'id' => $msg->id,
            'role' => $msg->role,
            'content' => $msg->content,
            'tokens_used' => $msg->tokens_used,
            'created_at' => $msg->created_at?->toIso8601String(),
        ]);

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'session_id' => $conversation->session_id,
                'ip_address' => $conversation->ip_address,
                'country' => $conversation->country,
                'country_code' => $conversation->country_code,
                'city' => $conversation->city,
                'user_agent' => $conversation->user_agent,
                'device_type' => $conversation->device_type,
                'messages_count' => $conversation->messages_count,
                'started_at' => $conversation->started_at?->toIso8601String(),
                'last_activity_at' => $conversation->last_activity_at?->toIso8601String(),
            ],
            'messages' => $messages,
        ]);
    }

    public function embeddingsJson(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $type = $request->get('type');

        $query = \Modules\ListingAiChatbot\Models\AiEmbedding::where('listing_id', $business->id);

        if ($type) {
            if ($type === 'restaurant_menu') {
                $query->whereIn('source_type', ['restaurant_category', 'restaurant_product']);
            } elseif ($type === 'appointments') {
                $query->whereIn('source_type', ['appointment', 'appointment_exception']);
            } else {
                $query->where('source_type', $type);
            }
        }

        $embeddings = $query
            ->orderBy('source_type')
            ->orderBy('id')
            ->limit(200)
            ->get(['id', 'source_type', 'source_id', 'chunk_text'])
            ->map(fn($e) => [
                'id' => $e->id,
                'source_type' => $e->source_type,
                'source_id' => $e->source_id,
                'chunk_text' => $e->chunk_text,
            ]);

        return response()->json(['embeddings' => $embeddings]);
    }
}
