<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Listings\Models\Listing;
use Modules\ListingRestaurantMenu\Entities\MenuCategory;
use Modules\ListingRestaurantMenu\Entities\MenuProduct;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuProductController extends Controller
{
    public function create(Request $request, Listing $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $categories = MenuCategory::where('listing_id', $business->id)
            ->where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'title' => $cat->nested_title,
                ];
            });

        return inertia('Member/MenuProducts/Create', [
            'listing' => $business,
            'categories' => $categories,
        ]);
    }

    public function index(Request $request, Listing $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $query = MenuProduct::where('listing_id', $business->id)->with('category', 'variants', 'images');

        $categoryId = $request->query('category');
        if ($categoryId) {
            $category = MenuCategory::where('id', $categoryId)
                ->where('listing_id', $business->id)
                ->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        } elseif ($request->boolean('uncategorized')) {
            $query->whereNull('category_id');
        }

        $search = $request->query('search');
        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $products = $query->orderBy('sort_order')->paginate(20);

        $categories = MenuCategory::where('listing_id', $business->id)
            ->where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'title' => $cat->nested_title,
                ];
            });

        return inertia('Member/MenuProducts/Index', [
            'listing' => $business,
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $request->boolean('uncategorized') ? 'uncategorized' : ($categoryId ?? null),
            'searchQuery' => $search,
        ]);
    }

    public function store(Request $request, Listing $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,png|max:10240',
            'base_price' => 'nullable|numeric|min:0|max:99999999',
            'show_price' => 'boolean',
            'featured' => 'boolean',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category = MenuCategory::find($validated['category_id']);
        abort_unless($category->listing_id === $business->id, 403);

        $validated['listing_id'] = $business->id;
        $validated['slug'] = MenuProduct::generateUniqueSlug($business->id, $validated['title']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products/' . $business->id, ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        }

        $product = MenuProduct::create($validated);

        $variantsData = $request->input('variants', []);
        $variantFiles = $request->file('variants', []);

        if (!empty($variantsData)) {
            foreach ($variantsData as $index => $variantData) {
                $variantImage = null;

                if (isset($variantFiles[$index]) && isset($variantFiles[$index]['image']) && $variantFiles[$index]['image']) {
                    $file = $variantFiles[$index]['image'];
                    $path = $file->store('products/' . $business->id . '/variants', ['disk' => 'public']);
                    $variantImage = Storage::disk('public')->url($path);
                }

                $product->variants()->create([
                    'title' => $variantData['title'] ?? '',
                    'price' => $variantData['price'] ?? 0,
                    'description' => $variantData['description'] ?? null,
                    'sort_order' => $variantData['sort_order'] ?? 0,
                    'active' => $variantData['active'] ?? true,
                    'image' => $variantImage,
                ]);
            }
        }

        return redirect()->route('member.menu.products.index', $business->id)
            ->with('success', 'Producto creado exitosamente.');
    }

    public function update(Request $request, Listing $business, MenuProduct $product)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->listing_id === $business->id, 403);

        $validated = $request->validate([
            'category_id' => 'required|exists:menu_categories,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|file|mimes:jpeg,png|max:10240',
            'base_price' => 'nullable|numeric|min:0|max:99999999',
            'show_price' => 'boolean',
            'featured' => 'boolean',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category = MenuCategory::find($validated['category_id']);
        abort_unless($category->listing_id === $business->id, 403);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products/' . $business->id, ['disk' => 'public']);
            $validated['image'] = Storage::disk('public')->url($path);
        } else {
            unset($validated['image']);
        }

        $product->update($validated);

        $variantsData = $request->input('variants', []);
        $variantFiles = $request->file('variants', []);

        if (!empty($variantsData)) {
            $product->variants()->delete();

            foreach ($variantsData as $index => $variantData) {
                $variantImage = null;

                if (isset($variantFiles[$index]) && isset($variantFiles[$index]['image']) && $variantFiles[$index]['image']) {
                    $file = $variantFiles[$index]['image'];
                    $path = $file->store('products/' . $business->id . '/variants', ['disk' => 'public']);
                    $variantImage = Storage::disk('public')->url($path);
                }

                $product->variants()->create([
                    'title' => $variantData['title'] ?? '',
                    'price' => $variantData['price'] ?? 0,
                    'description' => $variantData['description'] ?? null,
                    'sort_order' => $variantData['sort_order'] ?? 0,
                    'active' => $variantData['active'] ?? true,
                    'image' => $variantImage,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Producto actualizado exitosamente.');
    }

    public function destroy(Listing $business, MenuProduct $product)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->listing_id === $business->id, 403);

        $product->variants()->delete();
        $product->images()->delete();
        $product->delete();

        return redirect()->back()->with('success', 'Producto eliminado exitosamente.');
    }

    public function edit(Request $request, Listing $business, MenuProduct $product)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->listing_id === $business->id, 403);

        $categories = MenuCategory::where('listing_id', $business->id)
            ->where('active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($cat) {
                return [
                    'id' => $cat->id,
                    'title' => $cat->nested_title,
                ];
            });

        return inertia('Member/MenuProducts/Edit', [
            'listing' => $business,
            'product' => $product->load('variants', 'images'),
            'categories' => $categories,
        ]);
    }

    public function reorder(Request $request, Listing $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('menu_products', 'id')->where('listing_id', $business->id)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        \DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                \Modules\ListingRestaurantMenu\Entities\MenuProduct::where('id', $id)
                    ->where('listing_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('menu_products', 'id')->where('listing_id', $business->id)],
        ]);

        $count = count($data['ids']);

        $products = \Modules\ListingRestaurantMenu\Entities\MenuProduct::where('listing_id', $business->id)
            ->whereIn('id', $data['ids'])
            ->get();

        foreach ($products as $product) {
            $product->variants()->delete();
            $product->images()->delete();
            $product->delete();
        }

        $message = $count === 1
            ? "1 producto eliminado correctamente."
            : "{$count} productos eliminados correctamente.";

        return redirect()->back()
            ->with('success', $message);
    }

    public function clone(Listing $business, MenuProduct $product)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->listing_id === $business->id, 403);

        $newProduct = $product->replicate();
        $newProduct->title = $product->title . ' (Copia)';
        $newProduct->slug = MenuProduct::generateUniqueSlug($business->id, $product->title . ' (Copia)');
        $newProduct->active = false;
        $newProduct->featured = false;
        $newProduct->save();

        foreach ($product->variants as $variant) {
            $newVariant = $variant->replicate();
            $newVariant->product_id = $newProduct->id;
            $newVariant->save();
        }

        foreach ($product->images as $image) {
            $newImage = $image->replicate();
            $newImage->product_id = $newProduct->id;
            $newImage->save();
        }

        return redirect()->back()
            ->with('success', 'Producto clonado exitosamente. Edita el producto para ver más detalles.');
    }
}