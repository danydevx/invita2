<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Businesses\Models\Business;
use Modules\RestaurantMenu\Entities\MenuCategory;
use Modules\RestaurantMenu\Entities\MenuProduct;

class MenuController extends Controller
{
    public function show(Request $request, string $businessSlug)
    {
        $business = Business::where('slug', $businessSlug)->firstOrFail();

        $categories = MenuCategory::where('business_id', $business->id)
            ->whereNull('parent_id')
            ->where('active', true)
            ->with(['children' => function ($query) {
                $query->where('active', true)->orderBy('sort_order');
            }, 'products' => function ($query) {
                $query->where('active', true)->orderBy('sort_order');
            }])
            ->orderBy('sort_order')
            ->get();

        $uncategorizedProducts = MenuProduct::where('business_id', $business->id)
            ->whereNull('category_id')
            ->where('active', true)
            ->with('variants', 'images')
            ->orderBy('sort_order')
            ->get();

        $allProducts = MenuProduct::where('business_id', $business->id)
            ->where('active', true)
            ->with('category', 'variants', 'images')
            ->orderBy('sort_order')
            ->get();

        $featuredProducts = MenuProduct::where('business_id', $business->id)
            ->where('featured', true)
            ->where('active', true)
            ->with('category', 'variants', 'images')
            ->limit(6)
            ->get();

        $search = $request->get('search');

        if ($search) {
            $products = MenuProduct::where('business_id', $business->id)
                ->where('active', true)
                ->where(function ($query) use ($search) {
                    $query->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                })
                ->with('category', 'variants', 'images')
                ->paginate(20);
        } else {
            $products = null;
        }

        return inertia('Public/Menu/Show', [
            'business' => $business,
            'categories' => $categories,
            'uncategorizedProducts' => $uncategorizedProducts,
            'allProducts' => $allProducts,
            'featuredProducts' => $featuredProducts,
            'searchResults' => $products,
            'search' => $search,
        ]);
    }
}