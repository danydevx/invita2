<?php

namespace Modules\ListingAiChatbot\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\ListingAiChatbot\Models\ChatbotPreset;
use Modules\ListingAiChatbot\Models\ChatbotPersonality;
use Modules\Listings\Models\Listing;
use Inertia\Inertia;

class ChatbotPresetController extends Controller
{
    public function index(Request $request)
    {
        $query = ChatbotPreset::query()
            ->with('creator:id,name')
            ->orderBy('is_system', 'desc')
            ->orderBy('name');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $presets = $query->paginate(20)->withQueryString();

        $businessTypes = Listing::select('listing_type')
            ->distinct()
            ->orderBy('listing_type')
            ->pluck('listing_type');

        return Inertia::render('Admin/AiChatbot/Presets/Index', [
            'presets' => $presets,
            'filters' => $request->only(['search', 'listing_type', 'is_active']),
            'businessTypes' => $businessTypes,
        ]);
    }

    public function create()
    {
        $businessTypes = Listing::select('listing_type')
            ->distinct()
            ->orderBy('listing_type')
            ->pluck('listing_type');

        $personalities = ChatbotPersonality::getActiveForSelect();

        return Inertia::render('Admin/AiChatbot/Presets/Create', [
            'businessTypes' => $businessTypes,
            'personalities' => $personalities,
            'languages' => ['es', 'en', 'pt', 'fr'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:chatbot_presets,slug',
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
            'is_active' => 'boolean',
        ]);

        $validated['is_system'] = false;
        $validated['created_by'] = auth()->id();

        ChatbotPreset::create($validated);

        return redirect()->route('admin.modules.listing-aichatbot.presets.index')
            ->with('success', 'Preset creado exitosamente.');
    }

    public function edit(ChatbotPreset $preset)
    {
        $businessTypes = Listing::select('listing_type')
            ->distinct()
            ->orderBy('listing_type')
            ->pluck('listing_type');

        $personalities = ChatbotPersonality::getActiveForSelect();

        return Inertia::render('Admin/AiChatbot/Presets/Edit', [
            'preset' => $preset,
            'businessTypes' => $businessTypes,
            'personalities' => $personalities,
            'languages' => ['es', 'en', 'pt', 'fr'],
        ]);
    }

    public function update(Request $request, ChatbotPreset $preset)
    {
        if ($preset->is_system) {
            return redirect()->back()->withErrors(['error' => 'No se puede editar un preset del sistema.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:chatbot_presets,slug,' . $preset->id,
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
            'is_active' => 'boolean',
        ]);

        $preset->update($validated);

        return redirect()->route('admin.modules.listing-aichatbot.presets.index')
            ->with('success', 'Preset actualizado exitosamente.');
    }

    public function destroy(ChatbotPreset $preset)
    {
        if ($preset->is_system) {
            return redirect()->back()->withErrors(['error' => 'No se puede eliminar un preset del sistema.']);
        }

        $preset->delete();

        return redirect()->route('admin.modules.listing-aichatbot.presets.index')
            ->with('success', 'Preset eliminado exitosamente.');
    }

    public function toggle(ChatbotPreset $preset)
    {
        $preset->update(['is_active' => !$preset->is_active]);

        return redirect()->back()
            ->with('success', $preset->is_active ? 'Preset activado.' : 'Preset desactivado.');
    }

    public function duplicate(ChatbotPreset $preset)
    {
        $newPreset = $preset->replicate();
        $newPreset->name = $preset->name . ' (Copia)';
        $newPreset->slug = Str::slug($preset->name) . '-copy-' . time();
        $newPreset->is_system = false;
        $newPreset->created_by = auth()->id();
        $newPreset->save();

        return redirect()->route('admin.modules.listing-aichatbot.presets.edit', $newPreset)
            ->with('success', 'Preset duplicado. Edítalo según sea necesario.');
    }
}
