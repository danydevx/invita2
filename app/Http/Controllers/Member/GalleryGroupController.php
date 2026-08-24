<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingGallery\Models\ListingGallery;

class GalleryGroupController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingGallery::class, $business]);

        $galleries = $business->galleries()
            ->withCount('images')
            ->with(['images' => fn ($q) => $q->limit(4)->orderBy('sort_order')])
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (ListingGallery $gallery) => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'is_primary' => (bool) $gallery->is_primary,
                'is_active' => (bool) $gallery->is_active,
                'sort_order' => (int) $gallery->sort_order,
                'images_count' => (int) $gallery->images_count,
                'thumbnails' => $gallery->images->map(fn ($img) => [
                    'id' => $img->id,
                    'path' => $img->path,
                ]),
            ]);

        return Inertia::render('Member/Galleries/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'galleries' => $galleries,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingGallery::class, $business]);

        return Inertia::render('Member/Galleries/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingGallery::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $gallery = DB::transaction(function () use ($business, $data) {
            if (! empty($data['is_primary'])) {
                ListingGallery::where('listing_id', $business->id)->update(['is_primary' => false]);
            }

            return $business->galleries()->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_primary' => (bool) ($data['is_primary'] ?? false),
                'is_active' => (bool) ($data['is_active'] ?? true),
                'sort_order' => (int) ($data['sort_order'] ?? 0),
            ]);
        });

        $activity->log('gallery_created', [
            'actor' => $request->user(),
            'subject' => $gallery,
            'description' => 'Galería creada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.galleries.index', $business->id)
            ->with('success', 'Galería creada correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingGallery $gallery)
    {
        abort_unless($gallery->listing_id === $business->id, 404);
        $this->authorize('update', [ListingGallery::class, $gallery]);

        return Inertia::render('Member/Galleries/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'gallery' => [
                'id' => $gallery->id,
                'name' => $gallery->name,
                'description' => $gallery->description,
                'is_primary' => (bool) $gallery->is_primary,
                'is_active' => (bool) $gallery->is_active,
                'sort_order' => (int) $gallery->sort_order,
            ],
        ]);
    }

    public function update(Request $request, Listing $business, ListingGallery $gallery, ActivityService $activity)
    {
        abort_unless($gallery->listing_id === $business->id, 404);
        $this->authorize('update', [ListingGallery::class, $gallery]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($business, $gallery, $data) {
            if (! empty($data['is_primary'])) {
                ListingGallery::where('listing_id', $business->id)
                    ->where('id', '!=', $gallery->id)
                    ->update(['is_primary' => false]);
            }

            $gallery->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_primary' => (bool) ($data['is_primary'] ?? $gallery->is_primary),
                'is_active' => (bool) ($data['is_active'] ?? $gallery->is_active),
                'sort_order' => (int) ($data['sort_order'] ?? $gallery->sort_order),
            ]);
        });

        $activity->log('gallery_updated', [
            'actor' => $request->user(),
            'subject' => $gallery,
            'description' => 'Galería actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.galleries.index', $business->id)
            ->with('success', 'Galería actualizada correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingGallery $gallery, ActivityService $activity)
    {
        abort_unless($gallery->listing_id === $business->id, 404);
        $this->authorize('delete', [ListingGallery::class, $gallery]);

        $activity->log('gallery_deleted', [
            'actor' => $request->user(),
            'subject' => $gallery,
            'description' => 'Galería eliminada',
        ]);

        $gallery->delete();

        return redirect()->route('member.listings.galleries.index', $business->id)
            ->with('success', 'Galería eliminada correctamente.');
    }

    public function setPrimary(Request $request, Listing $business, ListingGallery $gallery, ActivityService $activity)
    {
        abort_unless($gallery->listing_id === $business->id, 404);
        $this->authorize('update', [ListingGallery::class, $gallery]);

        DB::transaction(function () use ($business, $gallery) {
            ListingGallery::where('listing_id', $business->id)
                ->where('id', '!=', $gallery->id)
                ->update(['is_primary' => false]);

            $gallery->update([
                'is_primary' => true,
                'is_active' => true,
            ]);
        });

        $activity->log('gallery_set_primary', [
            'actor' => $request->user(),
            'subject' => $gallery,
            'description' => 'Galería marcada como principal',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.galleries.index', $business->id)
            ->with('success', 'Galería marcada como principal.');
    }
}
