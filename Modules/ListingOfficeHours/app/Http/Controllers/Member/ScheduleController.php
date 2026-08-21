<?php

namespace Modules\ListingOfficeHours\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingLocations\Models\ListingLocation;
use Modules\ListingOfficeHours\Models\ListingSchedule;

class ScheduleController extends Controller
{
    public function indexAll(Request $request, Listing $listing)
    {
        $user = $request->user();

        $this->authorize('viewAny', [ListingSchedule::class, $listing]);

        $businessesData = Listing::where('user_id', $user->id)
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
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'businesses' => $businessesData,
        ]);
    }

    public function index(Request $request, Listing $listing, ListingLocation $location)
    {
        $this->authorize('viewAny', [ListingSchedule::class, $listing]);

        $perPage = $request->input('per_page', 10);
        $sort = $request->input('sort', 'name');
        $direction = $request->input('direction', 'asc');
        $search = $request->input('search', '');

        $query = $location->schedules()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy($sort, $direction)
            ->orderBy('name');

        $schedules = $query->paginate($perPage);

        $dataTable = [
            'data' => $schedules->items(),
            'current_page' => $schedules->currentPage(),
            'last_page' => $schedules->lastPage(),
            'per_page' => $schedules->perPage(),
            'total' => $schedules->total(),
            'from' => $schedules->firstItem(),
            'to' => $schedules->lastItem(),
        ];

        return Inertia::render('Member/OfficeHours/Index', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
            ],
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Listing $listing, ListingLocation $location)
    {
        $this->authorize('create', [ListingSchedule::class, $listing]);

        return Inertia::render('Member/OfficeHours/Create', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'location' => [
                'id' => $location->id,
                'name' => $location->name,
            ],
        ]);
    }

    public function store(Request $request, Listing $listing, ListingLocation $location)
    {
        $this->authorize('create', [ListingSchedule::class, $listing]);

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
            'listing_id' => $listing->id,
        ]);

        return redirect()->route('member.listings.locations.schedules.index', [$listing->id, $location->id])
            ->with('success', 'Horario creado correctamente.');
    }

    public function edit(Request $request, Listing $listing, ListingLocation $location, ListingSchedule $schedule)
    {
        $this->authorize('update', [ListingSchedule::class, $listing]);

        return Inertia::render('Member/OfficeHours/Edit', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
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

    public function update(Request $request, Listing $listing, ListingLocation $location, ListingSchedule $schedule)
    {
        $this->authorize('update', [ListingSchedule::class, $listing]);

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

        return redirect()->route('member.listings.locations.schedules.index', [$listing->id, $location->id])
            ->with('success', 'Horario actualizado correctamente.');
    }

    public function clone(Request $request, Listing $listing, ListingLocation $location, ListingSchedule $schedule)
    {
        $this->authorize('create', [ListingSchedule::class, $listing]);

        $newSchedule = $location->schedules()->create([
            'listing_id' => $listing->id,
            'name' => 'Copia de ' . $schedule->name,
            'days_of_week' => $schedule->days_of_week,
            'opening_time' => $schedule->opening_time,
            'closing_time' => $schedule->closing_time,
            'lunch_start_time' => $schedule->lunch_start_time,
            'lunch_end_time' => $schedule->lunch_end_time,
            'is_active' => false,
        ]);

        return redirect()->route('member.listings.locations.schedules.edit', [$listing->id, $location->id, $newSchedule->id])
            ->with('success', 'Horario clonado. Edítalo y guarda los cambios.');
    }

    public function destroy(Request $request, Listing $listing, ListingLocation $location, ListingSchedule $schedule)
    {
        $this->authorize('delete', $schedule);

        $schedule->delete();

        return redirect()->route('member.listings.locations.schedules.index', [$listing->id, $location->id])
            ->with('success', 'Horario eliminado correctamente.');
    }
}