<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleDefinition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ModuleDefinitionController extends Controller
{
    private const MAX_IMAGE_SIZE_KB = 2048;

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $definitions = ModuleDefinition::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('key', 'like', "%{$search}%");
                });
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        $definitions->getCollection()->transform(function ($def) {
            $def->settings_url = $def->settings_url ?? ($def->has_settings ? '/admin/modules/'.$def->key.'/settings' : null);

            return $def;
        });

        return Inertia::render('Admin/ModuleDefinitions/Index', [
            'definitions' => $definitions,
            'filters' => ['search' => $search],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/ModuleDefinitions/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:100', 'unique:listing_module_definitions,key'],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'has_settings' => ['boolean'],
            'settings_url' => ['nullable', 'string', 'max:255'],
            'is_premium' => ['boolean'],
            'show_in_menu' => ['boolean'],
            'menu_title' => ['nullable', 'string', 'max:50'],
        ]);

        ModuleDefinition::create([
            'key' => trim($data['key']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'has_settings' => (bool) ($data['has_settings'] ?? false),
            'settings_url' => $data['settings_url'] ?? null,
            'is_premium' => (bool) ($data['is_premium'] ?? false),
            'is_active' => true,
            'show_in_menu' => (bool) ($data['show_in_menu'] ?? true),
            'menu_title' => $data['menu_title'] ?? null,
        ]);

        return redirect()->route('admin.business-module-definitions.index')
            ->with('success', 'Modulo creado correctamente.');
    }

    public function edit(ModuleDefinition $definition)
    {
        $settingsUrl = $definition->settings_url;
        if (! $settingsUrl && $definition->has_settings) {
            $settingsUrl = '/admin/modules/'.$definition->key.'/settings';
        }

        $imageUrl = null;
        if ($definition->image) {
            $imageUrl = Storage::disk('public')->url($definition->image);
        }

        return Inertia::render('Admin/ModuleDefinitions/Edit', [
            'definition' => [
                'id' => $definition->id,
                'key' => $definition->key,
                'name' => $definition->name,
                'description' => $definition->description,
                'icon' => $definition->icon,
                'image' => $imageUrl,
                'sort_order' => $definition->sort_order,
                'has_settings' => (bool) $definition->has_settings,
                'settings_url' => $settingsUrl,
                'is_active' => (bool) $definition->is_active,
                'is_premium' => (bool) $definition->is_premium,
                'show_in_menu' => (bool) $definition->show_in_menu,
                'menu_title' => $definition->menu_title ?? '',
            ],
        ]);
    }

    public function update(Request $request, ModuleDefinition $definition)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:100', 'unique:listing_module_definitions,key,'.$definition->id],
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'icon' => ['nullable', 'string', 'max:100'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,gif,webp', 'max:'.self::MAX_IMAGE_SIZE_KB],
            'remove_image' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'has_settings' => ['boolean'],
            'settings_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'is_premium' => ['boolean'],
            'show_in_menu' => ['boolean'],
            'menu_title' => ['nullable', 'string', 'max:50'],
        ]);

        if ($request->boolean('remove_image')) {
            if ($definition->image) {
                Storage::disk('public')->delete($definition->image);
            }
            $definition->image = null;
        } elseif ($request->hasFile('image')) {
            if ($definition->image) {
                Storage::disk('public')->delete($definition->image);
            }
            $path = $request->file('image')->store('module-definitions/'.$definition->id, ['disk' => 'public']);
            $definition->image = $path;
        }

        $definition->update([
            'key' => trim($data['key']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'icon' => $data['icon'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'has_settings' => (bool) ($data['has_settings'] ?? false),
            'settings_url' => $data['settings_url'] ?? null,
            'is_active' => (bool) ($data['is_active'] ?? false),
            'is_premium' => (bool) ($data['is_premium'] ?? false),
            'show_in_menu' => (bool) ($data['show_in_menu'] ?? false),
            'menu_title' => $data['menu_title'] ?? null,
        ]);

        return redirect()->route('admin.business-module-definitions.index')
            ->with('success', 'Modulo actualizado correctamente.');
    }

    public function destroy(ModuleDefinition $definition)
    {
        $definition->delete();

        return redirect()->route('admin.business-module-definitions.index')
            ->with('success', 'Modulo eliminado correctamente.');
    }
}
