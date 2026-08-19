<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $userFilter = trim((string) $request->input('user', ''));
        $type = trim((string) $request->input('type', ''));
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        $activities = Activity::query()
            ->with(['user:id,name,email', 'actor:id,name,email'])
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->when($userFilter !== '', function ($query) use ($userFilter) {
                $needle = mb_strtolower($userFilter);
                $query->where(function ($q) use ($needle) {
                    $q->whereHas('user', function ($userQuery) use ($needle) {
                        $userQuery->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                            ->orWhereRaw('LOWER(email) like ?', ['%'.$needle.'%']);
                    })->orWhereHas('actor', function ($actorQuery) use ($needle) {
                        $actorQuery->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                            ->orWhereRaw('LOWER(email) like ?', ['%'.$needle.'%']);
                    });
                });
            })
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('created_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('created_at', '<=', $dateTo))
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($activity) => [
                'id' => $activity->id,
                'type' => $activity->type,
                'description' => $activity->description,
                'user' => $activity->user ? [
                    'id' => $activity->user->id,
                    'name' => $activity->user->name,
                    'email' => $activity->user->email,
                ] : null,
                'actor' => $activity->actor ? [
                    'id' => $activity->actor->id,
                    'name' => $activity->actor->name,
                    'email' => $activity->actor->email,
                ] : null,
                'subject_type' => $activity->subject_type,
                'subject_id' => $activity->subject_id,
                'ip_address' => $activity->ip_address,
                'created_at' => $activity->created_at?->toDateTimeString(),
            ]);

        $types = Activity::query()
            ->select('type')
            ->distinct()
            ->orderBy('type')
            ->pluck('type')
            ->values();

        return Inertia::render('Admin/Activity/Index', [
            'activities' => $activities,
            'types' => $types,
            'filters' => [
                'user' => $userFilter,
                'type' => $type,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }
}
