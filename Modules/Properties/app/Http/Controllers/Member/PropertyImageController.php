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

    public function store(Request $request, Business $business, Property $property)
    {
        $user = Auth::user();
        abort_unless($property->listing_id === $business->id, 403);
        abort_unless($business->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin']), 403);

        $files = $request->file('images');
        if (!$files) {
            return redirect()->back()->with('error', 'No se encontraron imágenes.');
        }

        $files = is_array($files) ? $files : [$files];

        $existingCount = $property->images()->count();
        $allowed = self::MAX_FILES - $existingCount;

        if (count($files) > $allowed) {
            return redirect()->back()->with('error', "Solo puedes subir hasta " . self::MAX_FILES . " imágenes en total.");
        }

        foreach ($files as $file) {
            if (!$file) continue;

            if ($file->getSize() > self::MAX_SIZE_KB * 1024) {
                return redirect()->back()->with('error', 'Cada imagen debe ser menor a ' . (self::MAX_SIZE_KB / 1024) . 'MB.');
            }

            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, self::ALLOWED_MIMES)) {
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

        return redirect()->back()->with('success', 'Imágenes subidas correctamente.');
    }

    public function destroy(Business $business, Property $property, PropertyImage $image)
    {
        $user = Auth::user();
        abort_unless($property->listing_id === $business->id, 403);
        abort_unless($business->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin']), 403);
        abort_unless($image->property_id === $property->id, 403);

        if ($image->image_path) {
            Storage::disk(self::DISK)->delete($image->image_path);
        }

        $image->delete();

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }
}