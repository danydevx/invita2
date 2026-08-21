<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingLocations\Models\ListingLocation;
use Modules\ListingProducts\Models\ListingProduct;

class ProductController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingProduct::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');
        $categoryId = $request->get('category');

        $allowedSorts = ['name', 'price', 'is_active', 'is_featured', 'sort_order', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->products()
            ->with('location', 'category')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                if ($categoryId === 'uncategorized') {
                    $q->whereNull('category_id');
                } else {
                    $q->where('category_id', $categoryId);
                }
            })
            ->orderBy($sort, $direction)
            ->orderBy('name');

        $products = $query->paginate($perPage);

        $products->getCollection()->transform(function ($product) {
            if ($product->image) {
                $product->image = "/storage/{$product->image}";
            }
            return $product;
        });

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $categories = $business->productCategories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $dataTable = [
            'data' => $products->items(),
            'current_page' => $products->currentPage(),
            'last_page' => $products->lastPage(),
            'per_page' => $products->perPage(),
            'total' => $products->total(),
            'from' => $products->firstItem(),
            'to' => $products->lastItem(),
        ];

        return Inertia::render('Member/Products/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'products' => $products,
            'locations' => $locations,
            'categories' => $categories,
            'dataTable' => $dataTable,
            'selectedCategory' => $categoryId,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingProduct::class, $business]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = $business->productCategories()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Products/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingProduct::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:150',
                Rule::unique('listing_products')->where('listing_id', $business->id),
            ],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg', 'max:2048'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'show_price' => ['boolean'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'whatsapp_contact' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'category_id' => ['nullable', 'exists:listing_product_categories,id'],
        ]);

        $data['listing_id'] = $business->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['slug']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        $product = $business->products()->create($data);

        $activity->log('product_created', [
            'actor' => $request->user(),
            'subject' => $product,
            'description' => 'Producto creado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.products.index', $business->id)
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingProduct $product)
    {
        $this->authorize('update', [ListingProduct::class, $product]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = $business->productCategories()->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        $productImages = $product->images()->orderBy('sort_order')->get(['id', 'path', 'filename', 'is_primary']);

        return Inertia::render('Member/Products/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'image' => $product->image,
                'price' => $product->price,
                'show_price' => $product->show_price ?? true,
                'compare_at_price' => $product->compare_at_price,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'quantity' => $product->quantity,
                'is_active' => $product->is_active,
                'is_featured' => $product->is_featured,
                'whatsapp_contact' => $product->whatsapp_contact,
                'sort_order' => $product->sort_order,
                'business_location_id' => $product->business_location_id,
                'category_id' => $product->category_id,
            ],
            'locations' => $locations,
            'categories' => $categories,
            'productImages' => $productImages->map(fn($img) => [
                'id' => $img->id,
                'url' => $img->path ? "/storage/{$img->path}" : null,
                'filename' => $img->filename,
                'is_primary' => $img->is_primary,
            ]),
        ]);
    }

    public function update(Request $request, Listing $business, ListingProduct $product, ActivityService $activity)
    {
        $this->authorize('update', [ListingProduct::class, $product]);

        \Log::info('Product update category_id received:', ['category_id' => $request->get('category_id')]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'slug' => [
                'required',
                'string',
                'max:150',
                Rule::unique('listing_products')->where('listing_id', $business->id)->ignore($product->id),
            ],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg', 'max:2048'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'show_price' => ['boolean'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0', 'max:99999999.99'],
            'sku' => ['nullable', 'string', 'max:100'],
            'barcode' => ['nullable', 'string', 'max:100'],
            'quantity' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'whatsapp_contact' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'category_id' => ['nullable', 'exists:listing_product_categories,id'],
        ]);

        $data['slug'] = \Illuminate\Support\Str::slug($data['slug']);

        if ($request->hasFile('image')) {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        } elseif ($request->input('_remove_image')) {
            if ($product->image) {
                \Storage::disk('public')->delete($product->image);
            }
            $data['image'] = null;
        }

        $product->update($data);

        $activity->log('product_updated', [
            'actor' => $request->user(),
            'subject' => $product,
            'description' => 'Producto actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.products.index', $business->id)
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingProduct $product, ActivityService $activity)
    {
        $this->authorize('delete', [ListingProduct::class, $product]);

        $activity->log('product_deleted', [
            'actor' => $request->user(),
            'subject' => $product,
            'description' => 'Producto eliminado',
        ]);

        $product->delete();

        return redirect()->route('member.listings.products.index', $business->id)
            ->with('success', 'Producto eliminado correctamente.');
    }

    public function clone(Request $request, Listing $business, ListingProduct $product)
    {
        $this->authorize('create', [ListingProduct::class, $business]);

        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copia)';
        $newProduct->slug = \Illuminate\Support\Str::slug($product->name) . '-' . time();
        $newProduct->save();

        return redirect()->route('member.listings.products.edit', [$business->id, $newProduct->id])
            ->with('success', 'Producto clonado correctamente.');
    }

    public function reorder(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('listing_products', 'id')->where('listing_id', $business->id)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        \DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                \Modules\ListingProducts\Models\ListingProduct::where('id', $id)
                    ->where('listing_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('listing_products', 'id')->where('listing_id', $business->id)],
        ]);

        $count = count($data['ids']);

        \Modules\ListingProducts\Models\ListingProduct::where('listing_id', $business->id)
            ->whereIn('id', $data['ids'])
            ->delete();

        $message = $count === 1
            ? "1 producto eliminado correctamente."
            : "{$count} productos eliminados correctamente.";

        return redirect()->back()
            ->with('success', $message);
    }
}
