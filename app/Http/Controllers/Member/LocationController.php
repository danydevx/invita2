<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingLocations\Models\ListingLocation;

class LocationController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingLocation::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'name');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['name', 'city', 'is_primary', 'is_active', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'name';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->locations()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%")
                      ->orWhere('address_line_1', 'like', "%{$search}%");
                });
            })
            ->orderByRaw("CASE WHEN is_primary = 1 THEN 0 ELSE 1 END")
            ->orderBy($sort, $direction);

        $locations = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($locations->items())->map(function ($loc) {
                return [
                    'id' => $loc->id,
                    'name' => $loc->name,
                    'address_line_1' => $loc->address_line_1,
                    'city' => $loc->city,
                    'phone' => $loc->phone,
                    'image' => $loc->image ? '/storage/' . $loc->image : null,
                    'is_primary' => $loc->is_primary,
                    'is_active' => $loc->is_active,
                ];
            })->toArray(),
            'current_page' => $locations->currentPage(),
            'last_page' => $locations->lastPage(),
            'per_page' => $locations->perPage(),
            'total' => $locations->total(),
            'from' => $locations->firstItem(),
            'to' => $locations->lastItem(),
        ];

        return Inertia::render('Member/Locations/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'locations' => $locations,
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingLocation::class, $business]);

        return Inertia::render('Member/Locations/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingLocation::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'state_code' => ['nullable', 'string', 'max:10'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'directions_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ], [], [
            'name' => 'nombre',
            'address_line_1' => 'dirección',
            'address_line_2' => 'dirección línea 2',
            'city' => 'ciudad',
            'state' => 'estado',
            'state_code' => 'código de estado',
            'municipality' => 'municipio',
            'postal_code' => 'código postal',
            'country' => 'país',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'latitude' => 'latitud',
            'longitude' => 'longitud',
            'directions_url' => 'URL de directions',
            'image' => 'imagen',
            'is_primary' => 'ubicación principal',
            'is_active' => 'ubicación activa',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('locations', 'public');
            $data['image'] = $path;
        }

        $location = $business->locations()->create($data);

        if (isset($data['is_primary']) && $data['is_primary']) {
            $business->locations()->where('id', '!=', $location->id)->update(['is_primary' => false]);
        }

        $activity->log('location_created', [
            'actor' => $request->user(),
            'subject' => $location,
            'description' => 'Ubicacion creada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.locations.index', $business->id)
            ->with('success', 'Ubicacion creada correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingLocation $location)
    {
        $this->authorize('update', [ListingLocation::class, $location]);

        return Inertia::render('Member/Locations/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
                'address_line_1' => $location->address_line_1,
                'address_line_2' => $location->address_line_2,
                'city' => $location->city,
                'state' => $location->state,
                'state_code' => $location->state_code,
                'municipality' => $location->municipality,
                'postal_code' => $location->postal_code,
                'country' => $location->country,
                'phone' => $location->phone,
                'email' => $location->email,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
                'directions_url' => $location->directions_url,
                'image' => $location->image ? '/storage/' . $location->image : null,
                'is_primary' => $location->is_primary,
                'is_active' => $location->is_active,
            ],
        ]);
    }

    public function update(Request $request, Listing $business, ListingLocation $location, ActivityService $activity)
    {
        $this->authorize('update', [ListingLocation::class, $location]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'state_code' => ['nullable', 'string', 'max:10'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country' => ['nullable', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:150'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'directions_url' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'remove_image' => ['nullable', 'boolean'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ], [], [
            'name' => 'nombre',
            'address_line_1' => 'dirección',
            'address_line_2' => 'dirección línea 2',
            'city' => 'ciudad',
            'state' => 'estado',
            'state_code' => 'código de estado',
            'municipality' => 'municipio',
            'postal_code' => 'código postal',
            'country' => 'país',
            'phone' => 'teléfono',
            'email' => 'correo electrónico',
            'latitude' => 'latitud',
            'longitude' => 'longitud',
            'directions_url' => 'URL de directions',
            'image' => 'imagen',
            'is_primary' => 'ubicación principal',
            'is_active' => 'ubicación activa',
        ]);

        if ($request->boolean('remove_image') && $location->image) {
            Storage::disk('public')->delete($location->image);
            $data['image'] = null;
        }

        if ($request->hasFile('image')) {
            if ($location->image) {
                Storage::disk('public')->delete($location->image);
            }
            $path = $request->file('image')->store('locations', 'public');
            $data['image'] = $path;
        }

        if (isset($data['is_primary']) && $data['is_primary']) {
            $business->locations()->where('id', '!=', $location->id)->update(['is_primary' => false]);
        }

        $location->update($data);

        $activity->log('location_updated', [
            'actor' => $request->user(),
            'subject' => $location,
            'description' => 'Ubicacion actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.locations.index', $business->id)
            ->with('success', 'Ubicacion actualizada correctamente.');
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $this->authorize('deleteAny', [\Modules\ListingLocations\Models\ListingLocation::class, $business]);

        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('listing_locations', 'id')->where('listing_id', $business->id)],
        ]);

        $count = \Modules\ListingLocations\Models\ListingLocation::where('listing_id', $business->id)
            ->whereIn('id', $request->ids)
            ->delete();

        return redirect()->back()->with('success', $count . ' ubicacion(es) eliminada(s).');
    }

    public function destroy(Request $request, Listing $business, ListingLocation $location, ActivityService $activity)
    {
        $this->authorize('delete', [ListingLocation::class, $location]);

        if ($location->image) {
            Storage::disk('public')->delete($location->image);
        }

        $activity->log('location_deleted', [
            'actor' => $request->user(),
            'subject' => $location,
            'description' => 'Ubicacion eliminada',
        ]);

        $location->delete();

        return redirect()->route('member.listings.locations.index', $business->id)
            ->with('success', 'Ubicacion eliminada correctamente.');
    }
}
