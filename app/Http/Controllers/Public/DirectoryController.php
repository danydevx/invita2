<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\ListingAppointments\Models\ListingAppointment;
use Modules\Listings\Enums\ListingType;
use Modules\Listings\Models\Listing;
use Modules\ListingLeads\Models\ListingLead;
use Modules\ListingLocations\Models\ListingLocation;
use Modules\ListingServices\Models\ListingService;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = [
            'search' => $request->input('search', ''),
            'type' => $request->input('type', ''),
            'location' => $request->input('location', ''),
            'lat' => $request->input('lat', ''),
            'lng' => $request->input('lng', ''),
            'radius' => $request->input('radius', '10'),
        ];

        $hasFilters = $request->filled('search') || $request->filled('type') || $request->filled('location');

        if (!$hasFilters) {
            $businessTypes = ListingType::cases();

            $featuredQuery = Listing::where('is_active', true)
                ->where('is_published', true)
                ->with(['locations' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->withCount(['reviews' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->withAvg(['reviews' => function ($q) {
                    $q->where('is_active', true);
                }], 'rating')
                ->orderByDesc('reviews_count')
                ->limit(8);

            $featuredBusinesses = $featuredQuery->get();

            $recentQuery = Listing::where('is_active', true)
                ->where('is_published', true)
                ->with(['locations' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->withCount(['reviews' => function ($q) {
                    $q->where('is_active', true);
                }])
                ->withAvg(['reviews' => function ($q) {
                    $q->where('is_active', true);
                }], 'rating')
                ->orderByDesc('created_at')
                ->limit(4);

            $recentBusinesses = $recentQuery->get();

            return view('public.home.index', [
                'title' => 'Directorio de Negocios',
                'description' => 'Encuentra los mejores negocios locales cerca de ti',
                'businessTypes' => $businessTypes,
                'featuredBusinesses' => $featuredBusinesses,
                'recentBusinesses' => $recentBusinesses,
            ]);
        }

        $query = Listing::where('is_active', true)
            ->where('is_published', true)
            ->with(['locations' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withCount(['reviews' => function ($q) {
                $q->where('is_active', true);
            }])
            ->withAvg(['reviews' => function ($q) {
                $q->where('is_active', true);
            }], 'rating');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->filled('type')) {
            $type = $request->input('type');
            $query->where('listing_type', $type);
        }

        if ($request->filled('location')) {
            $location = $request->input('location');
            $query->whereHas('locations', function ($q) use ($location) {
                $q->where('city', 'like', "%{$location}%")
                    ->orWhere('postal_code', 'like', "%{$location}%")
                    ->orWhere('municipality', 'like', "%{$location}%");
            });
        }

        if ($request->filled('lat') && $request->filled('lng') && $request->filled('radius')) {
            $lat = (float) $request->input('lat');
            $lng = (float) $request->input('lng');
            $radius = (float) $request->input('radius');

            $query->whereHas('locations', function ($q) use ($lat, $lng, $radius) {
                $q->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->selectRaw(
                        '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                        [$lat, $lng, $lat]
                    )
                    ->having('distance', '<=', $radius);
            });
        }

        $businesses = $query->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $businessTypes = ListingType::cases();

        $mapMarkers = $businesses->getCollection()->map(function ($business) {
            $locations = $business->locations->filter(function ($loc) {
                return $loc->latitude && $loc->longitude;
            });

            return $locations->map(function ($location) use ($business) {
                return [
                    'id' => $business->id,
                    'name' => $business->name,
                    'slug' => $business->slug,
                    'type' => $business->listing_type->label(),
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'address' => $location->address_line_1.', '.$location->city,
                ];
            });
        })->flatten(1)->values();

        return view('public.directory.index', [
            'title' => 'Directorio de Negocios',
            'description' => 'Encuentra los mejores negocios locales cerca de ti',
            'businesses' => $businesses,
            'businessTypes' => $businessTypes,
            'mapMarkers' => $mapMarkers,
            'filters' => $filters,
        ]);
    }

    public function show(string $slug)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $locations = $business->locations()
            ->where('is_active', true)
            ->get();

        $services = $business->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->orderBy('name')
            ->get(['id', 'name', 'description', 'price', 'duration_minutes']);

        $galleryImages = $business->galleryImages()
            ->where('is_active', true)
            ->whereHas('gallery', function ($query) {
                $query->where('is_primary', true)->where('is_active', true);
            })
            ->orderBy('sort_order')
            ->limit(12)
            ->get(['id', 'path', 'title']);

        $reviews = $business->reviews()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get(['id', 'client_name', 'rating', 'comment', 'created_at']);

        $avgRating = $business->reviews()
            ->where('is_active', true)
            ->avg('rating');

        $mapLocations = $locations->filter(function ($loc) {
            return $loc->latitude && $loc->longitude;
        })->map(function ($location) {
            return [
                'name' => $location->name,
                'address' => $location->address_line_1.', '.$location->city.' '.$location->postal_code,
                'phone' => $location->phone,
                'latitude' => $location->latitude,
                'longitude' => $location->longitude,
            ];
        })->values();

        return view('public.directory.show', [
            'title' => $business->name,
            'description' => $business->description,
            'business' => $business,
            'locations' => $locations,
            'services' => $services,
            'galleryImages' => $galleryImages,
            'reviews' => $reviews,
            'avgRating' => round($avgRating ?? 0, 1),
            'mapLocations' => $mapLocations,
        ]);
    }

    public function storeAppointment(Request $request, string $slug)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $data = $request->validate([
            'service_id' => ['required', 'exists:listing_services,id'],
            'location_id' => ['required', 'exists:listing_locations,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $service = ListingService::findOrFail($data['service_id']);
        $location = ListingLocation::findOrFail($data['location_id']);

        $endTime = date('H:i', strtotime($data['start_time'].' + '.$service->duration_minutes.' minutes'));

        $appointment = ListingAppointment::create([
            'listing_id' => $business->id,
            'business_location_id' => $location->id,
            'business_service_id' => $service->id,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => $endTime,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Cita solicitada correctamente. Te contactaremos pronto para confirmar.');
    }

    public function storeContact(Request $request, string $slug)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $lead = ListingLead::create([
            'listing_id' => $business->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => $data['message'],
            'source' => 'directory',
            'status' => 'new',
        ]);

        return redirect()->back()->with('success', 'Mensaje enviado correctamente.');
    }
}
