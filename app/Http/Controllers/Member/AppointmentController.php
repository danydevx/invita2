<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Appointments\Models\BusinessAppointment;
use Modules\Appointments\Enums\AppointmentStatus;
use Modules\Services\Models\BusinessService;
use Modules\Locations\Models\BusinessLocation;

class AppointmentController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessAppointment::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'appointment_date');
        $direction = $request->get('direction', 'desc');

        $allowedSorts = ['appointment_date', 'start_time', 'customer_name', 'status', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'appointment_date';
        }
        $direction = strtolower($direction) === 'asc' ? 'asc' : 'desc';

        $query = $business->appointments()
            ->with(['location', 'service'])
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_email', 'like', "%{$search}%")
                      ->orWhereHas('service', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
                });
            })
            ->orderBy($sort, $direction);

        $appointments = $query->paginate($perPage);

        $services = $business->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price']);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1']);

        $dataTable = [
            'data' => collect($appointments->items())->map(function ($apt) {
                return [
                    'id' => $apt->id,
                    'customer_name' => $apt->customer_name,
                    'customer_email' => $apt->customer_email,
                    'customer_phone' => $apt->customer_phone,
                    'appointment_date' => $apt->appointment_date,
                    'start_time' => $apt->start_time,
                    'end_time' => $apt->end_time,
                    'status' => $apt->status->value,
                    'status_label' => $apt->status->label(),
                    'notes' => $apt->notes,
                    'service' => $apt->service ? [
                        'id' => $apt->service->id,
                        'name' => $apt->service->name,
                    ] : null,
                    'location' => $apt->location ? [
                        'id' => $apt->location->id,
                        'name' => $apt->location->name,
                    ] : null,
                ];
            })->toArray(),
            'current_page' => $appointments->currentPage(),
            'last_page' => $appointments->lastPage(),
            'per_page' => $appointments->perPage(),
            'total' => $appointments->total(),
            'from' => $appointments->firstItem(),
            'to' => $appointments->lastItem(),
        ];

        return Inertia::render('Member/Appointments/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'appointments' => $appointments,
            'services' => $services,
            'locations' => $locations,
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessAppointment::class, $business]);

        $services = $business->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price']);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1']);

        return Inertia::render('Member/Appointments/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'services' => $services,
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity, AvailabilityService $availability)
    {
        $this->authorize('create', [BusinessAppointment::class, $business]);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'business_service_id' => ['required', 'exists:business_services,id'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'string'],
            'notes' => ['nullable', 'string'],
        ]);

        $normalizedStartTime = $this->normalizeTime($data['start_time']);
        if (!$normalizedStartTime) {
            return back()->withErrors(['start_time' => 'Hora inválida.']);
        }
        $data['start_time'] = $normalizedStartTime;

        $service = BusinessService::findOrFail($data['business_service_id']);

        $slotCheck = $availability->isSlotAvailable(
            $business,
            $data['appointment_date'],
            $data['start_time'],
            null,
            $service->duration_minutes
        );
        if (!$slotCheck['available']) {
            return back()->withErrors(['start_time' => $slotCheck['reason']]);
        }

        $endTime = date('H:i', strtotime($data['start_time'] . ' + ' . $service->duration_minutes . ' minutes'));

        $appointment = $business->appointments()->create([
            'business_id' => $business->id,
            'business_location_id' => $data['business_location_id'] ?? null,
            'business_service_id' => $data['business_service_id'],
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => $endTime,
            'status' => AppointmentStatus::PENDING,
            'notes' => $data['notes'] ?? null,
        ]);

        $activity->log('appointment_created', [
            'actor' => $request->user(),
            'subject' => $appointment,
            'description' => 'Cita creada manualmente',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.appointments.index', $business->id)
            ->with('success', 'Cita creada correctamente.');
    }

    public function show(Request $request, Business $business, BusinessAppointment $appointment)
    {
        $this->authorize('viewAny', [BusinessAppointment::class, $business]);

        $appointment->load(['location', 'service']);

        return Inertia::render('Member/Appointments/Show', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'appointment' => [
                'id' => $appointment->id,
                'customer_name' => $appointment->customer_name,
                'customer_email' => $appointment->customer_email,
                'customer_phone' => $appointment->customer_phone,
                'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'status' => $appointment->status->value,
                'status_label' => $appointment->status->label(),
                'notes' => $appointment->notes,
                'location' => $appointment->location ? [
                    'id' => $appointment->location->id,
                    'name' => $appointment->location->name,
                ] : null,
                'service' => $appointment->service ? [
                    'id' => $appointment->service->id,
                    'name' => $appointment->service->name,
                ] : null,
            ],
        ]);
    }

    public function edit(Request $request, Business $business, BusinessAppointment $appointment)
    {
        $this->authorize('update', [BusinessAppointment::class, $appointment]);

        $appointment->load(['location', 'service']);

        $services = $business->services()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price']);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1']);

        return Inertia::render('Member/Appointments/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'appointment' => [
                'id' => $appointment->id,
                'customer_name' => $appointment->customer_name,
                'customer_email' => $appointment->customer_email,
                'customer_phone' => $appointment->customer_phone,
                'appointment_date' => $appointment->appointment_date->format('Y-m-d'),
                'start_time' => $appointment->start_time,
                'end_time' => $appointment->end_time,
                'status' => $appointment->status->value,
                'notes' => $appointment->notes,
                'business_service_id' => $appointment->business_service_id,
                'business_location_id' => $appointment->business_location_id,
            ],
            'services' => $services,
            'locations' => $locations,
        ]);
    }

    public function update(Request $request, Business $business, BusinessAppointment $appointment, ActivityService $activity, AvailabilityService $availability)
    {
        $this->authorize('update', [BusinessAppointment::class, $appointment]);

        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'business_service_id' => ['required', 'exists:business_services,id'],
            'business_location_id' => ['nullable', 'exists:business_locations,id'],
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required', 'string'],
            'status' => ['required', 'string', 'in:pending,confirmed,cancelled,completed,no_show'],
            'notes' => ['nullable', 'string'],
        ]);

        $normalizedStartTime = $this->normalizeTime($data['start_time']);
        if (!$normalizedStartTime) {
            return back()->withErrors(['start_time' => 'Hora inválida.']);
        }
        $data['start_time'] = $normalizedStartTime;

        $service = BusinessService::findOrFail($data['business_service_id']);

        $slotCheck = $availability->isSlotAvailable(
            $business,
            $data['appointment_date'],
            $data['start_time'],
            $appointment->id,
            $service->duration_minutes
        );
        if (!$slotCheck['available']) {
            return back()->withErrors(['start_time' => $slotCheck['reason']]);
        }

        $endTime = date('H:i', strtotime($data['start_time'] . ' + ' . $service->duration_minutes . ' minutes'));

        $appointment->update([
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'business_service_id' => $data['business_service_id'],
            'business_location_id' => $data['business_location_id'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => $endTime,
            'status' => AppointmentStatus::from($data['status']),
            'notes' => $data['notes'] ?? null,
            'cancelled_at' => $data['status'] === 'cancelled' ? now() : null,
        ]);

        $activity->log('appointment_updated', [
            'actor' => $request->user(),
            'subject' => $appointment,
            'description' => 'Cita actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.businesses.appointments.index', $business->id)
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessAppointment $appointment, ActivityService $activity)
    {
        $this->authorize('delete', [BusinessAppointment::class, $appointment]);

        $activity->log('appointment_deleted', [
            'actor' => $request->user(),
            'subject' => $appointment,
            'description' => 'Cita eliminada',
        ]);

        $appointment->delete();

        return redirect()->route('member.businesses.appointments.index', $business->id)
            ->with('success', 'Cita eliminada correctamente.');
    }

    public function cancel(Request $request, Business $business, BusinessAppointment $appointment, ActivityService $activity)
    {
        $this->authorize('cancel', [BusinessAppointment::class, $appointment]);

        $appointment->update([
            'status' => AppointmentStatus::CANCELLED,
            'cancelled_at' => now(),
        ]);

        $activity->log('appointment_cancelled', [
            'actor' => $request->user(),
            'subject' => $appointment,
            'description' => 'Cita cancelada',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Cita cancelada correctamente.');
    }

    public function bulkDelete(Request $request, Business $business)
    {
        $this->authorize('deleteAny', [BusinessAppointment::class, $business]);

        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $count = count($data['ids']);

        $business->appointments()
            ->whereIn('id', $data['ids'])
            ->delete();

        $message = $count === 1
            ? "1 cita eliminada correctamente."
            : "{$count} citas eliminadas correctamente.";

        return redirect()->back()
            ->with('success', $message);
    }

    public function reschedule(Request $request, Business $business, BusinessAppointment $appointment, ActivityService $activity, AvailabilityService $availability)
    {
        $this->authorize('update', [BusinessAppointment::class, $appointment]);

        $data = $request->validate([
            'appointment_date' => ['required', 'date'],
            'start_time' => ['required', 'string'],
        ]);

        $normalizedStartTime = $this->normalizeTime($data['start_time']);
        if (!$normalizedStartTime) {
            return back()->withErrors(['start_time' => 'Hora inválida.']);
        }
        $data['start_time'] = $normalizedStartTime;

        $service = $appointment->service;

        $slotCheck = $availability->isSlotAvailable(
            $business,
            $data['appointment_date'],
            $data['start_time'],
            $appointment->id,
            $service ? $service->duration_minutes : 0
        );
        if (!$slotCheck['available']) {
            return back()->withErrors(['start_time' => $slotCheck['reason']]);
        }

        $endTime = date('H:i', strtotime($data['start_time'] . ' + ' . ($service ? $service->duration_minutes : 0) . ' minutes'));

        $appointment->update([
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => $endTime,
        ]);

        $activity->log('appointment_rescheduled', [
            'actor' => $request->user(),
            'subject' => $appointment,
            'description' => 'Cita reprogramada',
            'request' => $request,
        ]);

        return back()->with('success', 'Cita reprogramada correctamente.');
    }

    private function normalizeTime(string $time): ?string
    {
        $time = trim($time);

        if (preg_match('/^(\d{1,2}):(\d{2})(?::(\d{2}))?$/', $time, $matches)) {
            $h = (int) $matches[1];
            $m = (int) $matches[2];
            if ($h < 0 || $h > 23 || $m < 0 || $m > 59) {
                return null;
            }
            return sprintf('%02d:%02d', $h, $m);
        }

        if (preg_match('/^(\d{1,2}):(\d{2}):(\d{2})$/', $time, $matches)) {
            $h = (int) $matches[1];
            $m = (int) $matches[2];
            $s = (int) $matches[3];
            if ($h < 0 || $h > 23 || $m < 0 || $m > 59 || $s < 0 || $s > 59) {
                return null;
            }
            return sprintf('%02d:%02d', $h, $m);
        }

        $timestamp = strtotime('1970-01-01 ' . $time);
        if ($timestamp !== false) {
            return date('H:i', $timestamp);
        }

        return null;
    }
}
