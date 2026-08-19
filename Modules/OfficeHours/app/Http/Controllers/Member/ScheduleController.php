<?php

namespace Modules\OfficeHours\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Locations\Models\BusinessLocation;
use Modules\OfficeHours\Models\BusinessSchedule;

class ScheduleController extends Controller
{
    public function indexAll(Request $request, Business $business)
    {
        $user = $request->user();

        $this->authorize('viewAny', [BusinessSchedule::class, $business]);

        $businessesData = Business::where('user_id', $user->id)
            ->with(['locations.schedules' => function ($query) {
                $query->where('is_active', true);
            }])
            ->get(['id', 'name'])
            ->map(function ($b) {
                return [
                    'id' => $b->id,
                    'name' => $b->name,
                    'locations' => $b->locations->map(function ($location) {
                        return [
                            'id' => $location->id,
                            'name' => $location->name,
                            'city' => $location->city,
                            'schedules' => $location->schedules->map(function ($schedule) {
                                return [
                                    'id' => $schedule->id,
                                    'name' => $schedule->name,
                                    'days_display' => $schedule->days_display,
                                    'time_display' => $schedule->time_display,
                                ];
                            })->toArray(),
                        ];
                    })->toArray(),
                ];
            })->toArray();

        return Inertia::render('Member/OfficeHours/IndexAll', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'businesses' => $businessesData,
        ]);
    }

    public function index(Request $request, Business $business, BusinessLocation $location)
    {
        $this->authorize('viewAny', [BusinessSchedule::class, $business]);

        $schedules = $location->schedules()
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        return Inertia::render('Member/OfficeHours/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
            ],
            'schedules' => $schedules,
        ]);
    }

    public function create(Request $request, Business $business, BusinessLocation $location)
    {
        $this->authorize('create', [BusinessSchedule::class, $business]);

        return Inertia::render('Member/OfficeHours/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
            ],
        ]);
    }

    public function store(Request $request, Business $business, BusinessLocation $location)
    {
        $this->authorize('create', [BusinessSchedule::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'min:0', 'max:6'],
            'opening_time' => ['required', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'closing_time' => ['required', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', 'after:opening_time'],
            'lunch_start_time' => ['nullable', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'lunch_end_time' => ['nullable', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'is_active' => ['boolean'],
        ], [], [
            'name' => 'nombre',
            'days_of_week' => 'días de la semana',
            'opening_time' => 'hora de apertura',
            'closing_time' => 'hora de cierre',
            'lunch_start_time' => 'inicio de almuerzo',
            'lunch_end_time' => 'fin de almuerzo',
            'is_active' => 'activo',
        ]);

        $schedule = $location->schedules()->create([
            ...$data,
            'business_id' => $business->id,
        ]);

        return redirect()->route('member.businesses.locations.schedules.index', [$business->id, $location->id])
            ->with('success', 'Horario creado correctamente.');
    }

    public function edit(Request $request, Business $business, BusinessLocation $location, BusinessSchedule $schedule)
    {
        $this->authorize('update', [BusinessSchedule::class, $business]);

        return Inertia::render('Member/OfficeHours/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
            ],
            'schedule' => [
                'id' => $schedule->id,
                'name' => $schedule->name,
                'days_of_week' => $schedule->days_of_week,
                'opening_time' => $schedule->opening_time,
                'closing_time' => $schedule->closing_time,
                'lunch_start_time' => $schedule->lunch_start_time,
                'lunch_end_time' => $schedule->lunch_end_time,
                'is_active' => $schedule->is_active,
            ],
        ]);
    }

    public function update(Request $request, Business $business, BusinessLocation $location, BusinessSchedule $schedule)
    {
        $this->authorize('update', [BusinessSchedule::class, $business]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'days_of_week' => ['nullable', 'array'],
            'days_of_week.*' => ['integer', 'min:0', 'max:6'],
            'opening_time' => ['required', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'closing_time' => ['required', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/', 'after:opening_time'],
            'lunch_start_time' => ['nullable', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'lunch_end_time' => ['nullable', 'string', 'regex:/^([0-1]?[0-9]|2[0-3]):[0-5][0-9]$/'],
            'is_active' => ['boolean'],
        ], [], [
            'name' => 'nombre',
            'days_of_week' => 'días de la semana',
            'opening_time' => 'hora de apertura',
            'closing_time' => 'hora de cierre',
            'lunch_start_time' => 'inicio de almuerzo',
            'lunch_end_time' => 'fin de almuerzo',
            'is_active' => 'activo',
        ]);

        $schedule->update($data);

        return redirect()->route('member.businesses.locations.schedules.index', [$business->id, $location->id])
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function clone(Request $request, Business $business, BusinessLocation $location, BusinessSchedule $schedule)
    {
        $this->authorize('create', [BusinessSchedule::class, $business]);

        $newSchedule = $location->schedules()->create([
            'business_id' => $business->id,
            'name' => 'Copia de ' . $schedule->name,
            'days_of_week' => $schedule->days_of_week,
            'opening_time' => $schedule->opening_time,
            'closing_time' => $schedule->closing_time,
            'lunch_start_time' => $schedule->lunch_start_time,
            'lunch_end_time' => $schedule->lunch_end_time,
            'is_active' => false,
        ]);

        return redirect()->route('member.businesses.locations.schedules.edit', [$business->id, $location->id, $newSchedule->id])
            ->with('success', 'Horario clonado. Edítalo y guarda los cambios.');
    }

    public function destroy(Request $request, Business $business, BusinessLocation $location, BusinessSchedule $schedule)
    {
        $this->authorize('delete', $schedule);

        $schedule->delete();

        return redirect()->route('member.businesses.locations.schedules.index', [$business->id, $location->id])
            ->with('success', 'Horario eliminado correctamente.');
    }
}
