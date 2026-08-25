<?php

namespace Modules\Properties\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Listings\Models\Listing;
use Modules\Properties\Models\Property;
use Modules\Properties\Models\PropertyImage;

class PropertyImageController extends Controller
{
    protected const DISK = 'public';
    protected const DIRECTORY = 'properties/gallery';
    protected const MAX_FILES = 10;
    protected const MAX_SIZE_KB = 5120;
    protected const ALLOWED_MIMES = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];

    public function store(Request $request, Listing $listing, Property $property)
    {
        $user = Auth::user();
        abort_unless($property->listing_id === $listing->id, 403);
        abort_unless($listing->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin']), 403);

        $files = $request->file('images');
        if (!$files) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'No se encontraron imágenes.'], 422);
            }
            return redirect()->back()->with('error', 'No se encontraron imágenes.');
        }

        $files = is_array($files) ? $files : [$files];

        $existingCount = $property->images()->count();
        $allowed = self::MAX_FILES - $existingCount;

        if (count($files) > $allowed) {
            if ($request->expectsJson()) {
                return response()->json(['error' => "Solo puedes subir hasta " . self::MAX_FILES . " imágenes en total."], 422);
            }
            return redirect()->back()->with('error', "Solo puedes subir hasta " . self::MAX_FILES . " imágenes en total.");
        }

        foreach ($files as $file) {
            if (!$file) continue;

            if ($file->getSize() > self::MAX_SIZE_KB * 1024) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Cada imagen debe ser menor a ' . (self::MAX_SIZE_KB / 1024) . 'MB.'], 422);
                }
                return redirect()->back()->with('error', 'Cada imagen debe ser menor a ' . (self::MAX_SIZE_KB / 1024) . 'MB.');
            }

            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, self::ALLOWED_MIMES)) {
                if ($request->expectsJson()) {
                    return response()->json(['error' => 'Solo se permiten archivos JPG, PNG o WebP.'], 422);
                }
                return redirect()->back()->with('error', 'Solo se permiten archivos JPG, PNG o WebP.');
            }

            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $filename = 'property_' . now()->format('YmdHis') . '_' . Str::random(8) . '.' . $extension;

            $path = $file->storeAs(self::DIRECTORY, $filename, self::DISK);

            $property->images()->create([
                'image_path' => $path,
                'alt_text' => null,
                'caption' => null,
                'is_main' => false,
                'sort_order' => $existingCount,
            ]);

            $existingCount++;
        }

        $propertyImages = $property->images()->get()->map(fn($img) => [
            'id' => $img->id,
            'url' => $img->image_path ? "/storage/{$img->image_path}" : '',
            'filename' => basename($img->image_path ?? ''),
            'is_main' => $img->is_main,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => 'Imágenes subidas correctamente.',
                'propertyImages' => $propertyImages,
            ]);
        }

        return redirect()->back()->with('success', 'Imágenes subidas correctamente.');
    }

    public function destroy(Request $request, Listing $listing, Property $property, PropertyImage $image)
    {
        $user = Auth::user();
        abort_unless($property->listing_id === $listing->id, 403);
        abort_unless($listing->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin']), 403);
        abort_unless($image->property_id === $property->id, 403);

        if ($image->image_path) {
            Storage::disk(self::DISK)->delete($image->image_path);
        }

        $image->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => 'Imagen eliminada correctamente.']);
        }

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }

    public function setMain(Request $request, Listing $listing, Property $property, PropertyImage $image)
    {
        $user = Auth::user();
        abort_unless($property->listing_id === $listing->id, 403);
        abort_unless($listing->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin']), 403);
        abort_unless($image->property_id === $property->id, 403);

        $property->images()->update(['is_main' => false]);
        $image->update(['is_main' => true]);

        if ($request->expectsJson()) {
            return response()->json(['success' => 'Imagen establecida como principal.']);
        }

        return redirect()->back()->with('success', 'Imagen establecida como principal.');
    }
}