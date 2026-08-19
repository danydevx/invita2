<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Businesses\Models\Business;
use Modules\RestaurantMenu\Entities\MenuCategory;
use Modules\RestaurantMenu\Entities\MenuCategoryImage;
use Illuminate\Support\Facades\Auth;

class MenuCategoryController extends Controller
{
    private const MAX_FILE_SIZE_KB = 5120;
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public function index(Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $categories = MenuCategory::where('business_id', $business->id)
            ->whereNull('parent_id')
            ->with('children.images', 'products', 'images')
            ->orderBy('sort_order')
            ->get();

        return inertia('Member/Categories/Index', [
            'business' => $business,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => ['nullable', 'file', 'max:' . self::MAX_FILE_SIZE_KB, 'mimetypes:' . implode(',', self::ALLOWED_MIME_TYPES)],
            'parent_id' => 'nullable|exists:menu_categories,id',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ], [
            'image.max' => 'La imagen supera el tamaño máximo de 5MB.',
            'image.mimetypes' => 'Solo se permiten imágenes (JPEG, PNG, WebP, GIF).',
        ]);

        $validated['business_id'] = $business->id;
        $validated['slug'] = MenuCategory::generateUniqueSlug($business->id, $validated['title']);

        $category = MenuCategory::create($validated);

        if ($request->hasFile('image')) {
            $this->saveCategoryImage($category, $request->file('image'));
        }

        return redirect()->back()->with('success', 'Categoría creada exitosamente.');
    }

    public function update(Request $request, Business $business, MenuCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->business_id === $business->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => ['nullable', 'file', 'max:' . self::MAX_FILE_SIZE_KB, 'mimetypes:' . implode(',', self::ALLOWED_MIME_TYPES)],
            'remove_image' => ['nullable', 'boolean'],
            'parent_id' => [
                'nullable',
                'exists:menu_categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value == $category->id) {
                        $fail('Una categoría no puede ser padre de sí misma.');
                    }
                },
            ],
            'active' => 'boolean',
            'sort_order' => 'integer',
        ], [
            'image.max' => 'La imagen supera el tamaño máximo de 5MB.',
            'image.mimetypes' => 'Solo se permiten imágenes (JPEG, PNG, WebP, GIF).',
        ]);

        $category->update($validated);

        if ($request->boolean('remove_image')) {
            $this->deleteCategoryImage($category);
        }

        if ($request->hasFile('image')) {
            $this->deleteCategoryImage($category);
            $this->saveCategoryImage($category, $request->file('image'));
        }

        return redirect()->back()->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Business $business, MenuCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->business_id === $business->id, 403);

        if ($category->children()->count() > 0) {
            return redirect()->back()->with('error', 'No se puede eliminar una categoría con subcategorías.');
        }

        $this->deleteCategoryImage($category);
        $category->products()->update(['category_id' => null]);

        $category->delete();

        return redirect()->back()->with('success', 'Categoría eliminada. Los productos fueron desvinculados.');
    }

    private function saveCategoryImage(MenuCategory $category, $file): void
    {
        $disk = 'public';
        $path = $file->store('menu-categories/' . $category->business_id, ['disk' => $disk]);

        $category->images()->create([
            'path' => Storage::disk($disk)->url($path),
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'extension' => $file->getClientOriginalExtension(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'sort_order' => 0,
        ]);
    }

    private function deleteCategoryImage(MenuCategory $category): void
    {
        foreach ($category->images as $image) {
            $path = str_replace(url('/') . '/storage/', '', $image->path);
            Storage::disk('public')->delete($path);
        }
        $category->images()->delete();
    }
}
