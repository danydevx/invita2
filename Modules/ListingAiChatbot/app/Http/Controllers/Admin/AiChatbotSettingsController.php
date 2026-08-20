<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\ListingAiChatbot\Models\ChatbotPreset;
use Modules\ListingAiChatbot\Models\ChatbotPersonality;
use Inertia\Inertia;

class AiChatbotSettingsController extends Controller
{
    public function show()
    {
        $stats = [
            'presets_count' => ChatbotPreset::count(),
            'personalities_count' => ChatbotPersonality::count(),
            'active_presets' => ChatbotPreset::where('is_active', true)->count(),
            'active_personalities' => ChatbotPersonality::where('is_active', true)->count(),
        ];

        return Inertia::render('Admin/AiChatbot/Settings', [
            'stats' => $stats,
        ]);
    }
}
