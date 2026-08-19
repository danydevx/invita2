<?php

namespace App\Http\Controllers\Admin;

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
        $slots = $business->appointmentSlots()
            ->with(['service', 'location'])
            ->orderBy('specific_date', 'desc')
            ->orderBy('start_time', 'desc')
            ->paginate(20);

        $services = $business->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Admin/BusinessContent/SlotsIndex', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'slots' => $slots,
            'services' => $services,
            'locations' => $locations,
        ]);
    }

    public function store(Request $request, Business $business, ActivityService $activity)
    {
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

        $activity->log('admin_slot_created', [
            'actor' => $request->user(),
            'subject' => $slot,
            'description' => 'Admin: Slot de cita creado',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Slot creado correctamente.');
    }

    public function update(Request $request, Business $business, BusinessAppointmentSlot $slot, ActivityService $activity)
    {
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

        $activity->log('admin_slot_updated', [
            'actor' => $request->user(),
            'subject' => $slot,
            'description' => 'Admin: Slot de cita actualizado',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Slot actualizado correctamente.');
    }

    public function destroy(Request $request, Business $business, BusinessAppointmentSlot $slot, ActivityService $activity)
    {
        if ($slot->specific_date && $slot->specific_date < now()->toDateString()) {
            return redirect()->back()->with('error', 'No se pueden eliminar slots de fechas pasadas.');
        }

        $slot->delete();

        $activity->log('admin_slot_deleted', [
            'actor' => $request->user(),
            'subject' => $slot,
            'description' => 'Admin: Slot de cita eliminado',
            'request' => $request,
        ]);

        return redirect()->back()->with('success', 'Slot eliminado correctamente.');
    }
}
