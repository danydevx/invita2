<?php

namespace Modules\Products\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Listings\Models\Listing;
use Modules\Products\Models\BusinessProduct;
use Modules\Products\Models\BusinessProductImage;

class ProductImageController extends Controller
{
    public function store(Request $request, Business $business, BusinessProduct $product)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin']), 403);
        abort_unless($product->listing_id === $business->id, 403);

        $files = $request->file('images');
        if (!$files) {
            return response()->json(['error' => 'No se encontraron imágenes.'], 422);
        }

        $files = is_array($files) ? $files : [$files];

        $existingCount = $product->images()->count();
        $maxFiles = 10;
        $allowed = $maxFiles - $existingCount;

        if (count($files) > $allowed) {
            return response()->json(['error' => "Solo puedes subir hasta {$maxFiles} imágenes en total."], 422);
        }

        foreach ($files as $file) {
            if (!$file) continue;

            $maxSize = 2048;
            if ($file->getSize() > $maxSize * 1024) {
                return response()->json(['error' => 'Cada imagen debe ser menor a 2MB.'], 422);
            }

            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg'])) {
                return response()->json(['error' => 'Solo se permiten archivos JPG o PNG.'], 422);
            }

            $path = $file->store('product-images', 'public');

            $product->images()->create([
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
                'original_name' => $file->getClientOriginalName(),
                'extension' => $file->getClientOriginalExtension(),
                'mime_type' => $mimeType,
                'size' => $file->getSize(),
                'sort_order' => $existingCount,
            ]);

            $existingCount++;
        }

        return redirect()->back()->with('success', 'Imágenes subidas correctamente.');
    }

    public function destroy(Business $business, BusinessProduct $product, BusinessProductImage $image)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin']), 403);
        abort_unless($product->listing_id === $business->id, 403);
        abort_unless($image->business_product_id === $product->id, 403);

        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }
}
