<?php

namespace App\Services;

use Carbon\Carbon;
use Modules\Businesses\Models\Business;
use Modules\Appointments\Models\BusinessAvailability;
use Modules\Appointments\Models\BusinessAvailabilityException;
use Modules\Appointments\Models\BusinessAppointment;

class AvailabilityService
{
    public function isDateAvailable(Business $business, string $date): bool
    {
        $carbon = Carbon::parse($date);

        $exception = $business->availabilityExceptions()
            ->where('exception_date', $date)
            ->first();

        if ($exception) {
            return (bool) $exception->is_available;
        }

        $dayOfWeek = $carbon->dayOfWeek;
        $schedule = $business->availability()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule) {
            return false;
        }

        return (bool) $schedule->is_available;
    }

    public function getTimeRange(Business $business, string $date): ?array
    {
        $carbon = Carbon::parse($date);

        $exception = $business->availabilityExceptions()
            ->where('exception_date', $date)
            ->first();

        if ($exception) {
            if (!$exception->is_available) {
                return null;
            }
            return [
                'start' => $exception->start_time ? substr($exception->start_time, 0, 5) : null,
                'end' => $exception->end_time ? substr($exception->end_time, 0, 5) : null,
            ];
        }

        $dayOfWeek = $carbon->dayOfWeek;
        $schedule = $business->availability()
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule || !$schedule->is_available) {
            return null;
        }

        return [
            'start' => $schedule->start_time ? substr($schedule->start_time, 0, 5) : null,
            'end' => $schedule->end_time ? substr($schedule->end_time, 0, 5) : null,
        ];
    }

    public function isSlotAvailable(
        Business $business,
        string $date,
        string $startTime,
        ?int $excludeAppointmentId = null,
        int $serviceDurationMinutes = 0
    ): array {
        $timeRange = $this->getTimeRange($business, $date);

        if (!$timeRange || !$timeRange['start'] || !$timeRange['end']) {
            return [
                'available' => false,
                'reason' => 'El negocio no está disponible en esta fecha.',
            ];
        }

        $startCarbon = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $startTime . ':00');
        $endCarbon = $startCarbon->copy()->addMinutes($serviceDurationMinutes > 0 ? $serviceDurationMinutes : 30);

        $businessStart = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timeRange['start'] . ':00');
        $businessEnd = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timeRange['end'] . ':00');

        if ($startCarbon->lt($businessStart) || $endCarbon->gt($businessEnd)) {
            return [
                'available' => false,
                'reason' => "El horario debe estar entre {$timeRange['start']} y {$timeRange['end']} y el servicio debe completar dentro del horario.",
            ];
        }

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $exception = $business->availabilityExceptions()
            ->where('exception_date', $date)
            ->first();

        $slotsPerSlot = 1;
        if ($exception && $exception->slots_per_slot !== null) {
            $slotsPerSlot = (int) $exception->slots_per_slot;
        } elseif (!$exception) {
            $schedule = $business->availability()
                ->where('day_of_week', $dayOfWeek)
                ->first();
            if ($schedule) {
                $slotsPerSlot = (int) $schedule->slots_per_slot;
            }
        }

        $overlappingCount = $business->appointments()
            ->where('id', '!=', $excludeAppointmentId ?? 0)
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->where(function ($query) use ($startTime, $endCarbon) {
                $endTimeStr = $endCarbon->format('H:i:s');
                $query->where('start_time', '<', $endTimeStr)
                      ->where('end_time', '>', $startTime);
            })
            ->count();

        if ($overlappingCount >= $slotsPerSlot) {
            return [
                'available' => false,
                'reason' => 'No hay cupos disponibles en este horario.',
            ];
        }

        return [
            'available' => true,
            'reason' => null,
        ];
    }

    public function getAvailableSlotsForDate(
        Business $business,
        string $date,
        int $serviceDurationMinutes = 30
    ): array {
        $timeRange = $this->getTimeRange($business, $date);

        if (!$timeRange || !$timeRange['start'] || !$timeRange['end']) {
            return [];
        }

        $dayOfWeek = Carbon::parse($date)->dayOfWeek;
        $exception = $business->availabilityExceptions()
            ->where('exception_date', $date)
            ->first();

        $slotDuration = 30;
        $slotsPerSlot = 1;

        if ($exception) {
            $slotDuration = $serviceDurationMinutes;
        } else {
            $schedule = $business->availability()
                ->where('day_of_week', $dayOfWeek)
                ->first();
            if ($schedule) {
                $slotDuration = (int) $schedule->slot_duration_minutes;
                $slotsPerSlot = (int) $schedule->slots_per_slot;
            }
        }

        if ($exception && $exception->slots_per_slot !== null) {
            $slotsPerSlot = (int) $exception->slots_per_slot;
        }

        $businessStart = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timeRange['start'] . ':00');
        $businessEnd = Carbon::createFromFormat('Y-m-d H:i:s', $date . ' ' . $timeRange['end'] . ':00');

        $existingAppointments = $business->appointments()
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->get(['start_time', 'end_time']);

        $slots = [];
        $current = $businessStart->copy();

        while ($current->copy()->addMinutes($serviceDurationMinutes)->lte($businessEnd)) {
            $slotEnd = $current->copy()->addMinutes($serviceDurationMinutes);
            $slotStartStr = $current->format('H:i:s');

            $overlappingCount = $existingAppointments->filter(function ($apt) use ($current, $slotEnd) {
                $aptStart = Carbon::createFromFormat('Y-m-d H:i:s', $apt->appointment_date->format('Y-m-d') . ' ' . $apt->start_time);
                $aptEnd = Carbon::createFromFormat('Y-m-d H:i:s', $apt->appointment_date->format('Y-m-d') . ' ' . $apt->end_time);
                return $aptStart->lt($slotEnd) && $aptEnd->gt($current);
            })->count();

            $slots[] = [
                'start_time' => $current->format('H:i'),
                'end_time' => $slotEnd->format('H:i'),
                'available' => $overlappingCount < $slotsPerSlot,
                'remaining_capacity' => max(0, $slotsPerSlot - $overlappingCount),
            ];

            $current->addMinutes($slotDuration);
        }

        return $slots;
    }

    public function getOccupancyForDate(Business $business, string $date): int
    {
        return $business->appointments()
            ->where('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->count();
    }
}