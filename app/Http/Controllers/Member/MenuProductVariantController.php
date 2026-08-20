<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Listings\Models\Listing;
use Modules\ListingRestaurantMenu\Entities\MenuProduct;
use Modules\ListingRestaurantMenu\Entities\MenuProductVariant;
use Illuminate\Support\Facades\Auth;

class MenuProductVariantController extends Controller
{
    public function store(Request $request, Listing $business, MenuProduct $product)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->listing_id === $business->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $product->variants()->create($validated);

        return redirect()->back()->with('success', 'Variante creada exitosamente.');
    }

    public function update(Request $request, Listing $business, MenuProduct $product, MenuProductVariant $variant)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->listing_id === $business->id, 403);
        abort_unless($variant->product_id === $product->id, 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $variant->update($validated);

        return redirect()->back()->with('success', 'Variante actualizada exitosamente.');
    }

    public function destroy(Listing $business, MenuProduct $product, MenuProductVariant $variant)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->listing_id === $business->id, 403);
        abort_unless($variant->product_id === $product->id, 403);

        $variant->delete();

        return redirect()->back()->with('success', 'Variante eliminada exitosamente.');
    }
}