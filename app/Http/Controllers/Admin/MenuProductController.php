<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Businesses\Models\Business;
use Modules\RestaurantMenu\Entities\MenuCategory;
use Modules\RestaurantMenu\Entities\MenuProduct;
use Modules\RestaurantMenu\Entities\MenuProductVariant;
use Illuminate\Support\Facades\Auth;

class MenuProductController extends Controller
{
    public function index(Request $request, Business $business, ?MenuCategory $category = null)
    {
        $query = MenuProduct::where('business_id', $business->id)->with('category', 'variants', 'images');

        if ($category) {
            abort_unless($category->business_id === $business->id, 403);
            $query->where('category_id', $category->id);
        } elseif ($request->boolean('uncategorized')) {
            $query->whereNull('category_id');
        }

        $products = $query->orderBy('sort_order')->paginate(20);

        $categories = MenuCategory::where('business_id', $business->id)
            ->where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'title' => $cat->nested_title,
                ];
            });

        return inertia('Admin/Products/Index', [
            'business' => $business,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $request->boolean('uncategorized') ? 'uncategorized' : ($category?->id ?? null),
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'base_price' => 'nullable|numeric|min:0',
            'show_price' => 'boolean',
            'featured' => 'boolean',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category = MenuCategory::find($validated['category_id']);
        abort_unless($category->business_id === $business->id, 403);

        $validated['business_id'] = $business->id;
        $validated['slug'] = MenuProduct::generateUniqueSlug($business->id, $validated['title']);

        $product = MenuProduct::create($validated);

        return redirect()->back()->with('success', 'Producto creado exitosamente.');
    }

    public function update(Request $request, Business $business, MenuProduct $product)
    {
        abort_unless($product->business_id === $business->id, 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'base_price' => 'nullable|numeric|min:0',
            'show_price' => 'boolean',
            'featured' => 'boolean',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category = MenuCategory::find($validated['category_id']);
        abort_unless($category->business_id === $business->id, 403);

        $product->update($validated);

        return redirect()->back()->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Business $business, MenuProduct $product)
    {
        abort_unless($product->business_id === $business->id, 403);

        $product->variants()->delete();
        $product->images()->delete();
        $product->delete();

        return redirect()->back()->with('success', 'Producto eliminado exitosamente.');
    }
}