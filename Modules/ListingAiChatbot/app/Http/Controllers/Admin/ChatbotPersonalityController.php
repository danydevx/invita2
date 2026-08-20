<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ListingAiChatbot\Models\ChatbotPersonality;
use Inertia\Inertia;

class ChatbotPersonalityController extends Controller
{
    public function index()
    {
        $personalities = ChatbotPersonality::sorted()->get();

        return Inertia::render('Admin/AiChatbot/Personalities/Index', [
            'personalities' => $personalities,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/AiChatbot/Personalities/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:50|unique:chatbot_personalities,key',
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'system_prompt_hint' => 'nullable|string',
            'default_temperature' => 'nullable|numeric|min:0|max:1',
            'default_response_length' => 'nullable|in:short,medium,long',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        ChatbotPersonality::create($validated);

        return redirect()->route('admin.modules.listing-aichatbot.personalities.index')
            ->with('success', 'Personalidad creada exitosamente.');
    }

    public function edit(ChatbotPersonality $personality)
    {
        return Inertia::render('Admin/AiChatbot/Personalities/Edit', [
            'personality' => $personality,
        ]);
    }

    public function update(Request $request, ChatbotPersonality $personality)
    {
        $validated = $request->validate([
            'key' => 'required|string|max:50|unique:chatbot_personalities,key,' . $personality->id,
            'display_name' => 'required|string|max:100',
            'description' => 'nullable|string|max:1000',
            'system_prompt_hint' => 'nullable|string',
            'default_temperature' => 'nullable|numeric|min:0|max:1',
            'default_response_length' => 'nullable|in:short,medium,long',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $personality->update($validated);

        return redirect()->route('admin.modules.listing-aichatbot.personalities.index')
            ->with('success', 'Personalidad actualizada exitosamente.');
    }

    public function destroy(ChatbotPersonality $personality)
    {
        $personality->delete();

        return redirect()->route('admin.modules.listing-aichatbot.personalities.index')
            ->with('success', 'Personalidad eliminada.');
    }
}
