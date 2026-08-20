<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\BookAppointmentRequest;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\ListingAppointments\Enums\AppointmentStatus;
use Modules\ListingAppointments\Models\ListingAppointment;
use Modules\Listings\Models\Listing;
use Modules\ListingLocations\Models\ListingLocation;
use Modules\ListingPackages\Models\ListingPackage;
use Modules\ListingServices\Models\ListingService;

class BookingWidgetController extends Controller
{
    public function __construct(
        protected AvailabilityService $availability
    ) {}

    public function activeBusinesses(Request $request): JsonResponse
    {
        $businesses = Listing::query()
            ->where('is_active', true)
            ->where('is_published', true)
            ->whereHas('modules', function ($query) {
                $query->where('is_enabled', true)
                    ->whereHas('moduleDefinition', function ($q) {
                        $q->where('key', 'appointments');
                    });
            })
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'logo_path']);

        return response()->json([
            'businesses' => $businesses,
        ]);
    }

    public function services(Listing $businessSlug, Request $request): JsonResponse
    {
        $services = $businessSlug->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price']);

        $locations = $businessSlug->locations()
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1', 'city']);

        return response()->json([
            'services' => $services,
            'locations' => $locations,
        ]);
    }

    public function packages(Listing $businessSlug): JsonResponse
    {
        $packages = ListingPackage::where('listing_id', $businessSlug->id)
            ->where('is_active', true)
            ->with('features')
            ->orderBy('name')
            ->get(['id', 'title', 'short_description', 'price', 'promo_price']);

        return response()->json([
            'packages' => $packages->map(function ($package) {
                return [
                    'id' => $package->id,
                    'title' => $package->title,
                    'short_description' => $package->short_description,
                    'price' => $package->price,
                    'promo_price' => $package->promo_price,
                    'features' => $package->features->pluck('name')->toArray(),
                ];
            }),
        ]);
    }

    public function slots(Listing $businessSlug, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['required', 'date', 'after_or_equal:today'],
            'service_id' => ['nullable', 'integer', 'exists:listing_services,id'],
        ]);

        $service = null;
        $duration = 30;

        if (!empty($validated['service_id'])) {
            $service = ListingService::where('listing_id', $businessSlug->id)
                ->where('id', $validated['service_id'])
                ->where('is_active', true)
                ->where('allows_online_booking', true)
                ->first();

            if (!$service) {
                return response()->json([
                    'error' => 'Servicio no encontrado o no disponible para reservas.',
                ], 422);
            }
            $duration = $service->duration_minutes;
        }

        $slots = $this->availability->getAvailableSlotsForDate(
            $businessSlug,
            $validated['date'],
            $duration
        );

        return response()->json([
            'slots' => $slots,
            'service' => $service ? [
                'id' => $service->id,
                'name' => $service->name,
                'duration_minutes' => $service->duration_minutes,
            ] : null,
        ]);
    }

    public function store(Listing $businessSlug, BookAppointmentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $service = null;
        $serviceDuration = 30;

        if (!empty($data['service_id'])) {
            $service = ListingService::where('listing_id', $businessSlug->id)
                ->where('id', $data['service_id'])
                ->where('is_active', true)
                ->where('allows_online_booking', true)
                ->first();

            if (!$service) {
                return response()->json([
                    'error' => 'Servicio no encontrado o no disponible para reservas.',
                ], 422);
            }
            $serviceDuration = $service->duration_minutes;
        }

        if (!empty($data['location_id'])) {
            $location = ListingLocation::where('listing_id', $businessSlug->id)
                ->where('id', $data['location_id'])
                ->where('is_active', true)
                ->first();

            if (!$location) {
                return response()->json([
                    'errors' => ['location_id' => ['Ubicación inválida.']],
                ], 422);
            }
        }

        $check = $this->availability->isSlotAvailable(
            $businessSlug,
            $data['appointment_date'],
            $data['start_time'],
            null,
            $serviceDuration
        );

        if (!$check['available']) {
            return response()->json([
                'errors' => ['start_time' => [$check['reason']]],
            ], 422);
        }

        $endTime = date('H:i', strtotime($data['start_time'] . ' + ' . $serviceDuration . ' minutes'));

        $appointment = ListingAppointment::create([
            'listing_id' => $businessSlug->id,
            'business_service_id' => $service?->id,
            'business_location_id' => $data['location_id'] ?? null,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => $endTime,
            'status' => AppointmentStatus::PENDING,
            'notes' => $data['notes'] ?? null,
        ]);

        return response()->json([
            'appointment_id' => $appointment->id,
            'message' => 'Cita reservada correctamente.',
        ], 201);
    }
}