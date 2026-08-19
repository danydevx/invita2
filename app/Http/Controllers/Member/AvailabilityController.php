<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Appointments\Models\BusinessAvailability;
use Modules\Appointments\Models\BusinessAvailabilityException;

class AvailabilityController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessAvailability::class, $business]);

        $availability = $business->availability()
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        $weeklySchedule = [];
        foreach (BusinessAvailability::DAY_NAMES as $day => $name) {
            $schedule = $availability->get($day);
            $weeklySchedule[] = [
                'day_of_week' => $day,
                'day_name' => $name,
                'is_available' => $schedule ? (bool) $schedule->is_available : false,
                'start_time' => $schedule && $schedule->start_time ? substr($schedule->start_time, 0, 5) : '09:00',
                'end_time' => $schedule && $schedule->end_time ? substr($schedule->end_time, 0, 5) : '18:00',
                'slot_duration_minutes' => $schedule ? (int) $schedule->slot_duration_minutes : 30,
                'slots_per_slot' => $schedule ? (int) $schedule->slots_per_slot : 1,
            ];
        }

        $exceptions = $business->availabilityExceptions()
            ->orderBy('exception_date', 'asc')
            ->get()
            ->map(function ($exception) {
                return [
                    'id' => $exception->id,
                    'exception_date' => $exception->exception_date->format('Y-m-d'),
                    'is_available' => (bool) $exception->is_available,
                    'start_time' => $exception->start_time ? substr($exception->start_time, 0, 5) : null,
                    'end_time' => $exception->end_time ? substr($exception->end_time, 0, 5) : null,
                    'reason' => $exception->reason,
                    'slots_per_slot' => $exception->slots_per_slot,
                ];
            });

        $appointmentCounts = $business->appointments()
            ->where('appointment_date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->where('appointment_date', '<=', now()->addMonths(2)->toDateString())
            ->selectRaw('appointment_date, COUNT(*) as count')
            ->groupBy('appointment_date')
            ->pluck('count', 'appointment_date')
            ->mapWithKeys(function ($count, $date) {
                return [\Carbon\Carbon::parse($date)->format('Y-m-d') => (int) $count];
            });

        return Inertia::render('Member/Appointments/Availability', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'weeklySchedule' => $weeklySchedule,
            'exceptions' => $exceptions,
            'appointmentCounts' => $appointmentCounts,
        ]);
    }

    public function updateWeekly(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('update', [BusinessAvailability::class, $business]);

        $data = $request->validate([
            'schedule' => ['required', 'array'],
            'schedule.*.day_of_week' => ['required', 'integer', 'min:0', 'max:6'],
            'schedule.*.is_available' => ['required', 'boolean'],
            'schedule.*.start_time' => ['nullable', 'string'],
            'schedule.*.end_time' => ['nullable', 'string'],
            'schedule.*.slot_duration_minutes' => ['required', 'integer', 'min:5', 'max:480'],
            'schedule.*.slots_per_slot' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        foreach ($data['schedule'] as $day) {
            $startTime = null;
            $endTime = null;

            if ($day['is_available']) {
                if (empty($day['start_time']) || empty($day['end_time'])) {
                    return back()->withErrors(['schedule' => 'Debe especificar hora inicio y fin para los días disponibles.']);
                }
                if ($day['start_time'] >= $day['end_time']) {
                    return back()->withErrors(['schedule' => 'La hora de fin debe ser posterior a la hora de inicio.']);
                }
                $startTime = $this->normalizeTime($day['start_time']);
                $endTime = $this->normalizeTime($day['end_time']);
            }

            BusinessAvailability::updateOrCreate(
                [
                    'business_id' => $business->id,
                    'day_of_week' => $day['day_of_week'],
                ],
                [
                    'is_available' => $day['is_available'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'slot_duration_minutes' => $day['slot_duration_minutes'],
                    'slots_per_slot' => $day['slots_per_slot'],
                ]
            );
        }

        $activity->log('availability_weekly_updated', [
            'actor' => $request->user(),
            'subject' => $business,
            'description' => 'Disponibilidad semanal actualizada',
            'request' => $request,
        ]);

        return back()->with('success', 'Disponibilidad semanal actualizada correctamente.');
    }

    public function storeException(Request $request, Business $business, ActivityService $activity)
    {
        $this->authorize('update', [BusinessAvailability::class, $business]);

        $data = $request->validate([
            'exception_date' => ['required', 'date', 'after_or_equal:today'],
            'is_available' => ['required', 'boolean'],
            'start_time' => ['nullable', 'string'],
            'end_time' => ['nullable', 'string'],
            'reason' => ['nullable', 'string', 'max:255'],
            'slots_per_slot' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $startTime = null;
        $endTime = null;

        if ($data['is_available']) {
            if (empty($data['start_time']) || empty($data['end_time'])) {
                return back()->withErrors(['start_time' => 'Debe especificar hora inicio y fin.']);
            }
            if ($data['start_time'] >= $data['end_time']) {
                return back()->withErrors(['end_time' => 'La hora de fin debe ser posterior a la hora de inicio.']);
            }
            $startTime = $this->normalizeTime($data['start_time']);
            $endTime = $this->normalizeTime($data['end_time']);
        }

        $exception = BusinessAvailabilityException::updateOrCreate(
            [
                'business_id' => $business->id,
                'exception_date' => $data['exception_date'],
            ],
            [
                'is_available' => $data['is_available'],
                'start_time' => $startTime,
                'end_time' => $endTime,
                'reason' => $data['reason'] ?? null,
                'slots_per_slot' => $data['slots_per_slot'] ?? null,
            ]
        );

        $activity->log('availability_exception_created', [
            'actor' => $request->user(),
            'subject' => $exception,
            'description' => 'Excepción de disponibilidad creada',
            'request' => $request,
        ]);

        return back()->with('success', 'Excepción guardada correctamente.');
    }

    public function destroyException(Request $request, Business $business, BusinessAvailabilityException $exception, ActivityService $activity)
    {
        $this->authorize('update', [BusinessAvailability::class, $business]);

        if ($exception->business_id !== $business->id) {
            abort(404);
        }

        $exception->delete();

        $activity->log('availability_exception_deleted', [
            'actor' => $request->user(),
            'subject' => $exception,
            'description' => 'Excepción de disponibilidad eliminada',
            'request' => $request,
        ]);

        return back()->with('success', 'Excepción eliminada correctamente.');
    }

    private function normalizeTime(string $time): ?string
    {
        $time = trim($time);
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $time, $matches)) {
            return sprintf('%02d:%02d', $matches[1], $matches[2]);
        }
        if (preg_match('/^(\d{1,2}):(\d{2}):(\d{2})$/', $time, $matches)) {
            return sprintf('%02d:%02d', $matches[1], $matches[2]);
        }
        $timestamp = strtotime('1970-01-01 ' . $time);
        if ($timestamp !== false) {
            return date('H:i', $timestamp);
        }
        return null;
    }
}