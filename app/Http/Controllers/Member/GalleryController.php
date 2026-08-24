<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingGallery\Models\ListingGallery;
use Modules\ListingGallery\Models\ListingGalleryImage;

class GalleryController extends Controller
{
    private const MAX_FILE_SIZE_KB = 10239;

    private const MAX_FILES_PER_UPLOAD = 10;

    private const ALLOWED_MIME_TYPES = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

    public function index(Request $request, Listing $business, ?ListingGallery $gallery = null)
    {
        $this->authorize('viewAny', [ListingGalleryImage::class, $business]);

        if (! $gallery) {
            $gallery = ListingGallery::primaryFor($business->id);
        }

        if (! $gallery || $gallery->listing_id !== $business->id) {
            return Inertia::render('Member/Galleries/Show', [
                'listing' => [
                    'id' => $business->id,
                    'name' => $business->name,
                ],
                'galleries' => $this->galleryOptions($business),
                'currentGallery' => null,
                'images' => ['data' => [], 'links' => null],
                'locations' => $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
                'maxSizeKb' => self::MAX_FILE_SIZE_KB,
                'maxFilesPerUpload' => self::MAX_FILES_PER_UPLOAD,
                'allowedTypes' => self::ALLOWED_MIME_TYPES,
            ]);
        }

        return $this->renderGallery($request, $business, $gallery);
    }

    public function show(Request $request, Listing $business, ListingGallery $gallery)
    {
        abort_unless($gallery->listing_id === $business->id, 404);
        $this->authorize('viewAny', [ListingGalleryImage::class, $business]);

        return $this->renderGallery($request, $business, $gallery);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingGalleryImage::class, $business]);

        $data = $request->validate([
            'files' => ['required_without:file', 'array', 'min:1', 'max:'.self::MAX_FILES_PER_UPLOAD],
            'files.*' => ['required', 'file', 'max:'.self::MAX_FILE_SIZE_KB, 'mimetypes:'.implode(',', self::ALLOWED_MIME_TYPES)],
            'file' => ['required_without:files', 'file', 'max:'.self::MAX_FILE_SIZE_KB, 'mimetypes:'.implode(',', self::ALLOWED_MIME_TYPES)],
            'business_gallery_id' => [
                'required',
                Rule::exists('listing_galleries', 'id')->where('listing_id', $business->id),
            ],
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'business_location_id' => [
                'nullable',
                Rule::exists('listing_locations', 'id')->where('listing_id', $business->id),
            ],
        ], [
            'files.max' => 'Solo puedes subir hasta 10 imágenes por vez.',
            'files.*.max' => 'Cada imagen debe pesar menos de 10MB.',
            'files.*.mimetypes' => 'Solo se permiten imágenes JPEG, PNG, WebP o GIF.',
            'file.max' => 'La imagen debe pesar menos de 10MB.',
            'file.mimetypes' => 'Solo se permiten imágenes JPEG, PNG, WebP o GIF.',
        ]);

        if ($request->hasFile('files') && $request->hasFile('file')) {
            return back()->withErrors(['files' => 'Selecciona imágenes usando un solo campo de carga.']);
        }

        $files = $request->hasFile('files') ? $request->file('files') : [$request->file('file')];
        $storedPaths = [];
        $createdImages = [];

        try {
            DB::transaction(function () use ($files, $business, $request, $activity, $data, &$storedPaths, &$createdImages) {
                foreach ($files as $file) {
                    $disk = 'public';
                    $path = $file->store('gallery/'.$business->id, ['disk' => $disk]);
                    $storedPaths[] = $path;

                    $image = $business->galleryImages()->create([
                        'listing_id' => $business->id,
                        'business_gallery_id' => $data['business_gallery_id'],
                        'path' => Storage::disk($disk)->url($path),
                        'filename' => basename($path),
                        'original_name' => $file->getClientOriginalName(),
                        'extension' => $file->getClientOriginalExtension(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'title' => $data['title'] ?? null,
                        'description' => $data['description'] ?? null,
                        'business_location_id' => $data['business_location_id'] ?? null,
                        'is_active' => true,
                        'sort_order' => 0,
                    ]);
                    $createdImages[] = $image;

                    $activity->log('gallery_image_uploaded', [
                        'actor' => $request->user(),
                        'subject' => $image,
                        'description' => 'Imagen subida a galeria',
                        'request' => $request,
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            Storage::disk('public')->delete($storedPaths);

            throw $exception;
        }

        $count = count($createdImages);

        return redirect()->back()->with(
            'success',
            $count === 1 ? 'Imagen subida correctamente.' : $count.' imágenes subidas correctamente.'
        );
    }

    public function update(Request $request, Listing $business, ListingGalleryImage $image, ActivityService $activity)
    {
        abort_unless($image->listing_id === $business->id, 404);
        $this->authorize('update', [ListingGalleryImage::class, $image]);

        $data = $request->validate([
            'business_gallery_id' => [
                'required',
                Rule::exists('listing_galleries', 'id')->where('listing_id', $business->id),
            ],
            'title' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
            'business_location_id' => [
                'nullable',
                Rule::exists('listing_locations', 'id')->where('listing_id', $business->id),
            ],
        ]);

        $image->update($data);

        $activity->log('gallery_image_updated', [
            'actor' => $request->user(),
            'subject' => $image,
            'description' => 'Imagen de galeria actualizada',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Imagen actualizada correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingGalleryImage $image, ActivityService $activity)
    {
        abort_unless($image->listing_id === $business->id, 404);
        $this->authorize('delete', [ListingGalleryImage::class, $image]);

        if ($image->path) {
            $path = str_replace(url('/').'/storage/', '', $image->path);
            Storage::disk('public')->delete($path);
        }

        $activity->log('gallery_image_deleted', [
            'actor' => $request->user(),
            'subject' => $image,
            'description' => 'Imagen de galeria eliminada',
        ]);

        $image->delete();

        return redirect()->back()->with('success', 'Imagen eliminada correctamente.');
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $this->authorize('deleteAny', [ListingGalleryImage::class, $business]);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => [
                'integer',
                Rule::exists('business_gallery_images', 'id')->where('listing_id', $business->id),
            ],
        ]);

        $images = ListingGalleryImage::where('listing_id', $business->id)
            ->whereIn('id', $data['ids'])
            ->get();

        foreach ($images as $image) {
            if ($image->path) {
                $path = str_replace(url('/').'/storage/', '', $image->path);
                Storage::disk('public')->delete($path);
            }
            $image->delete();
        }

        return redirect()->back()->with('success', count($images).' imagen(es) eliminada(s).');
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
            'ids.*' => [
                'integer',
                Rule::exists('listing_gallery_images', 'id')->where('listing_id', $business->id),
            ],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                \Modules\ListingGallery\Models\ListingGalleryImage::where('id', $id)
                    ->where('listing_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }

    private function renderGallery(Request $request, Listing $business, ListingGallery $gallery): \Inertia\Response
    {
        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'sort_order');
        $direction = strtolower((string) $request->get('direction', 'asc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['title', 'sort_order', 'is_active', 'created_at'];
        if (! in_array($sort, $allowedSorts, true)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->galleryImages()
            ->where('business_gallery_id', $gallery->id)
            ->with('location')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderByDesc('id');

        $images = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($images->items())->map(function ($img) {
                return [
                    'id' => $img->id,
                    'path' => $img->path,
                    'title' => $img->title,
                    'description' => $img->description,
                    'sort_order' => $img->sort_order,
                    'is_active' => $img->is_active,
                    'location' => $img->location ? [
                        'id' => $img->location->id,
                        'name' => $img->location->name,
                    ] : null,
                ];
            })->toArray(),
            'current_page' => $images->currentPage(),
            'last_page' => $images->lastPage(),
            'per_page' => $images->perPage(),
            'total' => $images->total(),
            'from' => $images->firstItem(),
            'to' => $images->lastItem(),
        ];

        return Inertia::render('Member/Galleries/Show', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'galleries' => $this->galleryOptions($business),
            'currentGallery' => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'is_primary' => (bool) $gallery->is_primary,
                'is_active' => (bool) $gallery->is_active,
                'sort_order' => (int) $gallery->sort_order,
            ],
            'images' => $images,
            'dataTable' => $dataTable,
            'locations' => $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'maxSizeKb' => self::MAX_FILE_SIZE_KB,
            'maxFilesPerUpload' => self::MAX_FILES_PER_UPLOAD,
            'allowedTypes' => self::ALLOWED_MIME_TYPES,
        ]);
    }

    private function galleryOptions(Listing $business)
    {
        return $business->galleries()
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->withCount('images')
            ->get(['id', 'name', 'is_primary', 'is_active']);
    }
}
