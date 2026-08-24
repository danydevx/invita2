<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingLocations\Models\ListingLocation;
use Modules\ListingServices\Models\ListingService;
use Modules\ListingServices\Models\ListingServiceCategory;
use Illuminate\Support\Facades\Storage;

class ServiceController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingService::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'price', 'duration_minutes', 'is_active', 'sort_order', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->services()
            ->with('location')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderBy('name');

        $services = $query->paginate($perPage);

        $services->getCollection()->transform(function ($service) {
            if ($service->image) {
                $service->image = "/storage/{$service->image}";
            }
            return $service;
        });

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $dataTable = [
            'data' => $services->items(),
            'current_page' => $services->currentPage(),
            'last_page' => $services->lastPage(),
            'per_page' => $services->perPage(),
            'total' => $services->total(),
            'from' => $services->firstItem(),
            'to' => $services->lastItem(),
        ];

        return Inertia::render('Member/Services/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'services' => $services,
            'locations' => $locations,
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingService::class, $business]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = ListingServiceCategory::where('listing_id', $business->id)->where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return Inertia::render('Member/Services/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingService::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg', 'max:2048'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['boolean'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'allows_online_booking' => ['boolean'],
            'whatsapp_contact' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'category_id' => ['nullable', 'exists:listing_service_categories,id'],
        ]);

        $data['listing_id'] = $business->id;
        $data['slug'] = \Illuminate\Support\Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $data['image'] = $path;
        }

        if (isset($data['category_id']) && $data['category_id'] === '') {
            $data['category_id'] = null;
        }

        $service = $business->services()->create($data);

        $activity->log('service_created', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Servicio creado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.services.index', $business->id)
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingService $service)
    {
        $this->authorize('update', [ListingService::class, $service]);

        $locations = $business->locations()->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $categories = ListingServiceCategory::where('listing_id', $business->id)->where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $serviceImages = $service->images()->orderBy('sort_order')->get(['id', 'path', 'filename', 'is_primary']);

        return Inertia::render('Member/Services/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'image' => $this->sanitizeServiceImage($service->image),
                'duration_minutes' => $service->duration_minutes,
                'price' => $service->price,
                'deposit_required' => $service->deposit_required,
                'deposit_amount' => $service->deposit_amount,
                'allows_online_booking' => $service->allows_online_booking,
                'whatsapp_contact' => $service->whatsapp_contact,
                'is_active' => $service->is_active,
                'sort_order' => $service->sort_order,
                'business_location_id' => $service->business_location_id,
                'category_id' => $service->category_id,
            ],
            'locations' => $locations,
            'categories' => $categories,
            'serviceImages' => $serviceImages->map(fn($img) => [
                'id' => $img->id,
                'url' => $img->path ? "/storage/{$img->path}" : null,
                'filename' => $img->filename,
                'is_primary' => $img->is_primary,
            ]),
        ]);
    }

    public function update(Request $request, Listing $business, ListingService $service, ActivityService $activity)
    {
        $this->authorize('update', [ListingService::class, $service]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg', 'max:2048'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'deposit_required' => ['boolean'],
            'deposit_amount' => ['nullable', 'numeric', 'min:0'],
            'allows_online_booking' => ['boolean'],
            'whatsapp_contact' => ['nullable', 'string', 'max:50'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'business_location_id' => ['nullable', 'exists:listing_locations,id'],
            'category_id' => ['nullable', 'exists:listing_service_categories,id'],
        ]);

        if (isset($data['name'])) {
            $data['slug'] = \Illuminate\Support\Str::slug($data['name']);
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('services', 'public');
            $data['image'] = $path;
        } elseif ($request->input('_remove_image')) {
            $data['image'] = null;
        } else {
            unset($data['image']);
        }

        if (isset($data['category_id']) && $data['category_id'] === '') {
            $data['category_id'] = null;
        }

        $service->update($data);

        $activity->log('service_updated', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Servicio actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.services.index', $business->id)
            ->with('success', 'Servicio actualizado correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingService $service, ActivityService $activity)
    {
        $this->authorize('delete', [ListingService::class, $service]);

        $activity->log('service_deleted', [
            'actor' => $request->user(),
            'subject' => $service,
            'description' => 'Servicio eliminado',
        ]);

        $service->delete();

        return redirect()->route('member.listings.services.index', $business->id)
            ->with('success', 'Servicio eliminado correctamente.');
    }

    public function clone(Request $request, Listing $business, \Modules\ListingServices\Models\ListingService $service)
    {
        $this->authorize('create', [\Modules\ListingServices\Models\ListingService::class, $business]);

        $maxOrder = $business->services()->max('sort_order') ?? 0;

        $cloned = $business->services()->create([
            'name' => $service->name . ' (copia)',
            'slug' => \Illuminate\Support\Str::slug($service->name) . '-copy-' . time(),
            'description' => $service->description,
            'image' => $service->image,
            'duration_minutes' => $service->duration_minutes,
            'price' => $service->price,
            'deposit_required' => $service->deposit_required,
            'deposit_amount' => $service->deposit_amount,
            'allows_online_booking' => $service->allows_online_booking,
            'whatsapp_contact' => $service->whatsapp_contact,
            'is_active' => false,
            'sort_order' => $maxOrder + 1,
            'business_location_id' => $service->business_location_id,
            'category_id' => $service->category_id,
        ]);

        return redirect()->route('member.listings.services.edit', [$business->id, $cloned->id]);
    }

    public function reorder(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            // allowed
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('business_services', 'id')->where('listing_id', $business->id)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        \DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                \Modules\ListingServices\Models\ListingService::where('id', $id)
                    ->where('listing_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }

    private function sanitizeServiceImage(?string $image): ?string
    {
        if ($image === null) {
            return null;
        }
        if (str_starts_with($image, 'data:')) {
            return null;
        }
        return $image;
    }
}
