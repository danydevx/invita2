<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\ListingAiChatbot\Models\ChatbotAnalytics;
use Modules\ListingAiChatbot\Models\ChatbotTopQuestion;
use Modules\ListingAiChatbot\Models\AiConversation;

class ChatbotAnalyticsController extends Controller
{
    public function index(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $period = $request->get('period', '30days');

        $totals = ChatbotAnalytics::getTotals($business->id, $period);
        $dailyStats = ChatbotAnalytics::getStats($business->id, $period);
        $topQuestions = ChatbotTopQuestion::getTopQuestions($business->id, 10);

        $geoStats = AiConversation::where('listing_id', $business->id)
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $deviceStats = AiConversation::where('listing_id', $business->id)
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->orderBy('count', 'desc')
            ->get();

        $dailyConversations = AiConversation::where('listing_id', $business->id)
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(started_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return inertia('Member/AiChatbot/AnalyticsTab', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'totals' => $totals,
            'dailyStats' => $dailyStats,
            'topQuestions' => $topQuestions,
            'geoStats' => $geoStats,
            'deviceStats' => $deviceStats,
            'dailyConversations' => $dailyConversations,
            'period' => $period,
        ]);
    }

    public function indexJson(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $period = $request->get('period', '30days');

        $totals = ChatbotAnalytics::getTotals($business->id, $period);
        $dailyStats = ChatbotAnalytics::getStats($business->id, $period);
        $topQuestions = ChatbotTopQuestion::getTopQuestions($business->id, 10);

        $geoStats = AiConversation::where('listing_id', $business->id)
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('country, COUNT(*) as count')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        $deviceStats = AiConversation::where('listing_id', $business->id)
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('device_type, COUNT(*) as count')
            ->groupBy('device_type')
            ->orderBy('count', 'desc')
            ->get();

        $dailyConversations = AiConversation::where('listing_id', $business->id)
            ->where('started_at', '>=', now()->subDays(30))
            ->selectRaw('DATE(started_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return response()->json([
            'totals' => $totals,
            'dailyStats' => $dailyStats,
            'topQuestions' => $topQuestions,
            'geoStats' => $geoStats,
            'deviceStats' => $deviceStats,
            'dailyConversations' => $dailyConversations,
            'period' => $period,
        ]);
    }
}
