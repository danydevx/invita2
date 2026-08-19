<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MinisiteTheme;
use Illuminate\Support\Facades\Validator;

class MinisiteThemeController extends Controller
{
    public function index()
    {
        $themes = MinisiteTheme::withCount('businesses')->orderBy('name')->get();

        return inertia('Admin/MinisiteThemes/Index', [
            'themes' => $themes,
        ]);
    }

    public function edit(MinisiteTheme $theme)
    {
        return inertia('Admin/MinisiteThemes/Edit', [
            'theme' => $theme,
        ]);
    }

    public function create()
    {
        return inertia('Admin/MinisiteThemes/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:minisite_themes,slug',
            'description' => 'nullable|string',
            'preview_image' => 'nullable|string',
            'css_variables' => 'required|array',
            'layout_config' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $theme = MinisiteTheme::create($validated);

        return redirect()->back()->with('success', 'Theme creado exitosamente.');
    }

    public function update(Request $request, MinisiteTheme $theme)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:minisite_themes,slug,' . $theme->id,
            'description' => 'nullable|string',
            'preview_image' => 'nullable|string',
            'css_variables' => 'sometimes|required|array',
            'layout_config' => 'sometimes|required|array',
            'section_config' => 'sometimes|required|array',
            'is_active' => 'boolean',
        ]);

        $theme->update($validated);

        return redirect()->back()->with('success', 'Theme actualizado exitosamente.');
    }

    public function destroy(MinisiteTheme $theme)
    {
        if ($theme->businesses()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar un theme que está en uso.');
        }

        $theme->delete();

        return redirect()->back()->with('success', 'Theme eliminado exitosamente.');
    }
}
