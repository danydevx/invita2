<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Modules\ListingAiChatbot\Models\ChatbotPreset;
use Modules\ListingAiChatbot\Models\ChatbotPersonality;
use Modules\ListingAiChatbot\Models\ListingAiSetting;
use Modules\ListingAiChatbot\Models\AiContext;
use Inertia\Inertia;

class ChatbotPresetsController extends Controller
{
    public function index(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $presets = ChatbotPreset::where(function ($q) use ($business) {
            $q->whereNull('listing_id')
              ->orWhere('listing_id', $business->id);
        })
        ->orderBy('is_system', 'desc')
        ->orderBy('name')
        ->get();

        $businessPresets = $presets->where('listing_id', $business->id);
        $globalPresets = $presets->whereNull('listing_id');

        return Inertia::render('Member/AiChatbot/Presets/Index', [
            'listing' => $business,
            'businessPresets' => $businessPresets->values(),
            'globalPresets' => $globalPresets->values(),
        ]);
    }

    public function create(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $personalities = ChatbotPersonality::getActiveForSelect();
        $contexts = AiContext::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn($c) => ['id' => (string) $c->id, 'title' => $c->title]);

        return Inertia::render('Member/AiChatbot/Presets/Create', [
            'listing' => $business,
            'personalities' => $personalities,
            'languages' => ['es', 'en', 'pt', 'fr'],
            'contexts' => $contexts,
        ]);
    }

    public function store(Request $request, \Modules\Listings\Models\Listing $business)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'listing_type' => 'nullable|string|max:50',
            'personality' => 'required|string|max:50',
            'language' => 'required|in:es,en,pt,fr',
            'system_prompt_template' => 'required|string',
            'chatbot_name_template' => 'nullable|string|max:100',
            'greeting_message' => 'nullable|string|max:1000',
            'fallback_message' => 'nullable|string|max:1000',
            'configuration' => 'nullable|array',
            'initial_suggestions' => 'nullable|array',
            'context_ids' => 'nullable|array',
            'context_ids.*' => 'string',
            'is_active' => 'boolean',
            'copied_from_id' => 'nullable|integer|exists:chatbot_presets,id',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        $uniqueSlug = ChatbotPreset::generateUniqueSlug($slug, null, $business->id);

        $preset = ChatbotPreset::create([
            'name' => $validated['name'],
            'slug' => $uniqueSlug,
            'description' => $validated['description'] ?? null,
            'listing_type' => $validated['listing_type'] ?? null,
            'personality' => $validated['personality'],
            'language' => $validated['language'],
            'system_prompt_template' => $validated['system_prompt_template'],
            'chatbot_name_template' => $validated['chatbot_name_template'] ?? null,
            'greeting_message' => $validated['greeting_message'] ?? null,
            'fallback_message' => $validated['fallback_message'] ?? null,
            'configuration' => $validated['configuration'] ?? [],
            'initial_suggestions' => $validated['initial_suggestions'] ?? [],
            'context_ids' => $validated['context_ids'] ?? [],
            'is_active' => $validated['is_active'] ?? true,
            'is_system' => false,
            'listing_id' => $business->id,
            'copied_from_id' => $validated['copied_from_id'] ?? null,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('member.business.listing-aichatbot.presets.edit', [$business, $preset])
            ->with('success', 'Preset creado exitosamente.');
    }

    public function edit(Request $request, \Modules\Listings\Models\Listing $business, ChatbotPreset $preset)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);
        abort_unless($preset->listing_id === $business->id, 403);

        $personalities = ChatbotPersonality::getActiveForSelect();
        $contexts = AiContext::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('title')
            ->get(['id', 'title'])
            ->map(fn($c) => ['id' => (string) $c->id, 'title' => $c->title]);

        return Inertia::render('Member/AiChatbot/Presets/Edit', [
            'listing' => $business,
            'preset' => $preset,
            'personalities' => $personalities,
            'languages' => ['es', 'en', 'pt', 'fr'],
            'contexts' => $contexts,
        ]);
    }

    public function update(Request $request, \Modules\Listings\Models\Listing $business, ChatbotPreset $preset)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);
        abort_unless($preset->listing_id === $business->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100',
            'description' => 'nullable|string|max:500',
            'listing_type' => 'nullable|string|max:50',
            'personality' => 'required|string|max:50',
            'language' => 'required|in:es,en,pt,fr',
            'system_prompt_template' => 'required|string',
            'chatbot_name_template' => 'nullable|string|max:100',
            'greeting_message' => 'nullable|string|max:1000',
            'fallback_message' => 'nullable|string|max:1000',
            'configuration' => 'nullable|array',
            'initial_suggestions' => 'nullable|array',
            'context_ids' => 'nullable|array',
            'context_ids.*' => 'string',
            'is_active' => 'boolean',
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        $uniqueSlug = ChatbotPreset::generateUniqueSlug($slug, $preset->id, $business->id);

        $preset->update([
            'name' => $validated['name'],
            'slug' => $uniqueSlug,
            'description' => $validated['description'] ?? null,
            'listing_type' => $validated['listing_type'] ?? null,
            'personality' => $validated['personality'],
            'language' => $validated['language'],
            'system_prompt_template' => $validated['system_prompt_template'],
            'chatbot_name_template' => $validated['chatbot_name_template'] ?? null,
            'greeting_message' => $validated['greeting_message'] ?? null,
            'fallback_message' => $validated['fallback_message'] ?? null,
            'configuration' => $validated['configuration'] ?? [],
            'initial_suggestions' => $validated['initial_suggestions'] ?? [],
            'context_ids' => $validated['context_ids'] ?? [],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return redirect()->back()->with('success', 'Preset actualizado.');
    }

    public function destroy(Request $request, \Modules\Listings\Models\Listing $business, ChatbotPreset $preset)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);
        abort_unless($preset->listing_id === $business->id, 403);
        abort_if($preset->is_system, 403, 'No se puede eliminar un preset del sistema.');

        $preset->delete();

        return redirect()->route('member.business.listing-aichatbot.presets.index', [$business])
            ->with('success', 'Preset eliminado.');
    }

    public function duplicate(Request $request, \Modules\Listings\Models\Listing $business, ChatbotPreset $preset)
    {
        abort_unless($business->user_id === Auth::id() || Auth::user()->hasRole('superadmin'), 403);

        $newPreset = $preset->replicate();
        $newPreset->name = $preset->name . ' (Copia)';
        $newPreset->slug = ChatbotPreset::generateUniqueSlug(Str::slug($preset->name), null, $business->id);
        $newPreset->is_system = false;
        $newPreset->listing_id = $business->id;
        $newPreset->copied_from_id = $preset->id;
        $newPreset->created_by = Auth::id();
        $newPreset->save();

        return redirect()->route('member.business.listing-aichatbot.presets.edit', [$business, $newPreset])
            ->with('success', 'Preset duplicado. Edítalo según sea necesario.');
    }
}
