<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Modules\About\Models\BusinessAbout;
use Modules\Businesses\Models\Business;

class AboutController extends Controller
{
    private const MAX_FILE_SIZE_KB = 5120;
    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessAbout::class, $business]);

        $about = $business->about;

        return Inertia::render('Member/About/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'about' => $about,
        ]);
    }

    public function update(Request $request, Business $business, ActivityService $activity)
    {
        $user = $request->user();
        if (!$user->hasAnyRole(['superadmin', 'admin']) && $business->user_id !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'file', 'max:' . self::MAX_FILE_SIZE_KB, 'mimetypes:' . implode(',', self::ALLOWED_MIME_TYPES)],
            'logo' => ['nullable', 'file', 'max:' . self::MAX_FILE_SIZE_KB, 'mimetypes:' . implode(',', self::ALLOWED_MIME_TYPES)],
            'remove_image' => ['nullable', 'boolean'],
            'remove_logo' => ['nullable', 'boolean'],
            'is_active' => ['boolean'],
        ], [
            'image.max' => 'La imagen supera el tamaño máximo de 5MB.',
            'image.mimetypes' => 'Solo se permiten imágenes (JPEG, PNG, WebP, GIF).',
            'logo.max' => 'El logotipo supera el tamaño máximo de 5MB.',
            'logo.mimetypes' => 'Solo se permiten imágenes (JPEG, PNG, WebP, GIF).',
        ]);

        $about = BusinessAbout::updateOrCreate(
            ['business_id' => $business->id],
            [
                'title' => $data['title'] ?? null,
                'subtitle' => $data['subtitle'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]
        );

        if ($request->boolean('remove_image')) {
            $this->deleteImage($about, 'image_path');
        }

        if ($request->boolean('remove_logo')) {
            $this->deleteImage($about, 'logo_path');
        }

        if ($request->hasFile('image')) {
            $this->deleteImage($about, 'image_path');
            $this->saveImage($about, $request->file('image'), 'image_path');
        }

        if ($request->hasFile('logo')) {
            $this->deleteImage($about, 'logo_path');
            $this->saveImage($about, $request->file('logo'), 'logo_path');
        }

        $activity->log('about_updated', [
            'actor' => $request->user(),
            'subject' => $about,
            'description' => 'About actualizado',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'About actualizado correctamente.');
    }

    private function saveImage(BusinessAbout $about, $file, string $field): void
    {
        $disk = 'public';
        $path = $file->store('about/' . $about->business_id, ['disk' => $disk]);
        $about->update([$field => Storage::disk($disk)->url($path)]);
    }

    private function deleteImage(BusinessAbout $about, string $field): void
    {
        if ($about->$field) {
            $path = str_replace(url('/') . '/storage/', '', $about->$field);
            Storage::disk('public')->delete($path);
            $about->update([$field => null]);
        }
    }
}
