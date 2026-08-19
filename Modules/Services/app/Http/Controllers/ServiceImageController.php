<?php

namespace Modules\Services\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Modules\Businesses\Models\Business;
use Modules\Services\Models\BusinessService;
use Modules\Services\Models\BusinessServiceImage;

class ServiceImageController extends Controller
{
    public function store(Request $request, Business $business, BusinessService $service)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin']), 403);
        abort_unless($service->business_id === $business->id, 403);

        $files = $request->file('images');
        if (!$files) {
            return redirect()->back()->with('error', 'No se encontraron imágenes.');
        }

        $files = is_array($files) ? $files : [$files];

        $existingCount = $service->images()->count();
        $maxFiles = 10;
        $allowed = $maxFiles - $existingCount;

        if (count($files) > $allowed) {
            return redirect()->back()->with('error', "Solo puedes subir hasta {$maxFiles} imágenes en total.");
        }

        foreach ($files as $file) {
            if (!$file) continue;

            $maxSize = 2048;
            if ($file->getSize() > $maxSize * 1024) {
                return redirect()->back()->with('error', 'Cada imagen debe ser menor a 2MB.');
            }

            $mimeType = $file->getMimeType();
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/jpg'])) {
                return redirect()->back()->with('error', 'Solo se permiten archivos JPG o PNG.');
            }

            $path = $file->store('service-images', 'public');

            $service->images()->create([
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

    public function destroy(Business $business, BusinessService $service, BusinessServiceImage $image)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id || $user->hasAnyRole(['superadmin', 'admin']), 403);
        abort_unless($service->business_id === $business->id, 403);
        abort_unless($image->business_service_id === $service->id, 403);

        if ($image->path) {
            Storage::disk('public')->delete($image->path);
        }

        $image->delete();

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }
}
