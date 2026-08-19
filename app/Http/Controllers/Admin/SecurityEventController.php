<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityEvent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SecurityEventController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'event_type' => ['nullable', 'string', 'max:150'],
            'user_id' => ['nullable', 'integer'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $query = SecurityEvent::query()->with(['user:id,name,email']);

        $eventType = $filters['event_type'] ?? '';
        $userId = $filters['user_id'] ?? '';
        $dateFrom = $filters['date_from'] ?? '';
        $dateTo = $filters['date_to'] ?? '';

        if ($eventType !== '') {
            $query->where('event_type', $eventType);
        }

        if ($userId !== '') {
            $query->where('user_id', $userId);
        }

        if ($dateFrom !== '') {
            $query->whereDate('created_at', '>=', $dateFrom);
        }

        if ($dateTo !== '') {
            $query->whereDate('created_at', '<=', $dateTo);
        }

        $events = $query
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($event) => [
                'id' => $event->id,
                'event_type' => $event->event_type,
                'description' => $event->description,
                'ip_address' => $event->ip_address,
                'created_at' => $event->created_at?->toDateTimeString(),
                'user' => $event->user ? [
                    'id' => $event->user->id,
                    'name' => $event->user->name,
                    'email' => $event->user->email,
                ] : null,
            ]);

        $types = SecurityEvent::query()
            ->select('event_type')
            ->distinct()
            ->orderBy('event_type')
            ->pluck('event_type')
            ->values();

        return Inertia::render('Admin/SecurityEvents/Index', [
            'events' => $events,
            'types' => $types,
            'filters' => [
                'event_type' => $eventType,
                'user_id' => $userId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function show(SecurityEvent $event)
    {
        $event->load(['user:id,name,email']);

        return Inertia::render('Admin/SecurityEvents/Show', [
            'event' => [
                'id' => $event->id,
                'event_type' => $event->event_type,
                'description' => $event->description,
                'ip_address' => $event->ip_address,
                'user_agent' => $event->user_agent,
                'metadata' => $event->metadata,
                'created_at' => $event->created_at?->toDateTimeString(),
                'user' => $event->user ? [
                    'id' => $event->user->id,
                    'name' => $event->user->name,
                    'email' => $event->user->email,
                ] : null,
            ],
        ]);
    }
}
