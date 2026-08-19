<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Businesses\Models\Business;
use Modules\RestaurantMenu\Entities\MenuProduct;
use Modules\RestaurantMenu\Entities\MenuProductImage;
use Illuminate\Support\Facades\Auth;

class MenuProductImageController extends Controller
{
    public function store(Request $request, Business $business, MenuProduct $product)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->business_id === $business->id, 403);

        $validated = $request->validate([
            'image' => 'required|string',
            'sort_order' => 'integer',
        ]);

        $product->images()->create($validated);

        return redirect()->back()->with('success', 'Imagen agregada exitosamente.');
    }

    public function update(Request $request, Business $business, MenuProduct $product, MenuProductImage $image)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->business_id === $business->id, 403);
        abort_unless($image->product_id === $product->id, 403);

        $validated = $request->validate([
            'image' => 'required|string',
            'sort_order' => 'integer',
        ]);

        $image->update($validated);

        return redirect()->back()->with('success', 'Imagen actualizada exitosamente.');
    }

    public function destroy(Business $business, MenuProduct $product, MenuProductImage $image)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($product->business_id === $business->id, 403);
        abort_unless($image->product_id === $product->id, 403);

        $image->delete();

        return redirect()->back()->with('success', 'Imagen eliminada exitosamente.');
    }
}