<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Appointments\Models\BusinessAppointmentSlot;
use Modules\Services\Models\BusinessService;
use Modules\Locations\Models\BusinessLocation;

class SlotController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [\Modules\Appointments\Models\BusinessAppointmentSlot::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'specific_date');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['specific_date', 'start_time', 'is_available', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'specific_date';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query = $business->appointmentSlots()
            ->with(['service', 'location'])
            ->when($search, function ($q) use ($search) {
                $q->whereHas('service', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction);

        $slots = $query->paginate($perPage);

        $services = $business->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $dataTable = [
            'data' => collect($slots->items())->map(function ($slot) {
                return [
                    'id' => $slot->id,
                    'service' => $slot->service ? [
                        'id' => $slot->service->id,
                        'name' => $slot->service->name,
                    ] : null,
                    'location' => $slot->location ? [
                        'id' => $slot->location->id,
                        'name' => $slot->location->name,
                    ] : null,
                    'day_of_week' => $slot->day_of_week,
                    'specific_date' => $slot->specific_date,
                    'start_time' => $slot->start_time,
                    'end_time' => $slot->end_time,
                    'slots_available' => $slot->slots_available,
                    'is_available' => $slot->is_available,
                ];
            })->toArray(),
            'current_page' => $slots->currentPage(),
            'last_page' => $slots->lastPage(),
            'per_page' => $slots->perPage(),
            'total' => $slots->total(),
            'from' => $slots->firstItem(),
            'to' => $slots->lastItem(),
        ];

        return Inertia::render('Member/Slots/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'slots' => $slots,
            'services' => $services,
            'locations' => $locations,
            'dataTable' => $dataTable,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('create', [\Modules\Appointments\Models\BusinessAppointmentSlot::class, $business]);

        $data = $request->validate([
            'business_service_id' => ['required', 'exists:business_services,id'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'day_of_week' => ['nullable', 'integer', 'min:0', 'max:6'],
            'specific_date' => ['nullable', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slots_available' => ['nullable', 'integer', 'min:1'],
        ]);

        $data['business_id'] = $business->id;
        $data['is_available'] = true;

        $slot = $business->appointmentSlots()->create($data);

        $activity->log('slot_created', [
            'actor' => $request->user(),
            'subject' => $slot,
            'description' => 'Slot de cita creado',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Slot creado correctamente.');
    }

    public function update(Request $request, Business $business, BusinessAppointmentSlot $slot, ActivityService $activity)
    {
        $this->authorize('update', $slot);

        $data = $request->validate([
            'business_service_id' => ['required', 'exists:business_services,id'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'day_of_week' => ['nullable', 'integer', 'min:0', 'max:6'],
            'specific_date' => ['nullable', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slots_available' => ['nullable', 'integer', 'min:1'],
            'is_available' => ['boolean'],
        ]);

        if (empty($data['day_of_week']) && empty($data['specific_date'])) {
            return redirect()->back()->with('error', 'Debe seleccionar una fecha o un día de la semana.');
        }

        $slot->update($data);

        $activity->log('slot_updated', [
            'actor' => $request->user(),
            'subject' => $slot,
            'description' => 'Slot de cita actualizado',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Slot actualizado correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessAppointmentSlot $slot, ActivityService $activity)
    {
        $this->authorize('delete', $slot);

        if ($slot->specific_date && $slot->specific_date < now()->toDateString()) {
            return redirect()->back()->with('error', 'No se pueden eliminar slots de fechas pasadas.');
        }

        $slot->delete();

        $activity->log('slot_deleted', [
            'actor' => $request->user(),
            'subject' => $slot,
            'description' => 'Slot de cita eliminado',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Slot eliminado correctamente.');
    }
}
