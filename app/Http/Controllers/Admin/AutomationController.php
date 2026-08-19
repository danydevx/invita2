<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Automation;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AutomationController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $automations = Automation::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('event_key', 'like', "%{$search}%")
                        ->orWhere('action_key', 'like', "%{$search}%");
                });
            })
            ->with(['runs' => fn ($query) => $query->orderByDesc('executed_at')->limit(1)])
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($automation) => [
                'id' => $automation->id,
                'name' => $automation->name,
                'event_key' => $automation->event_key,
                'action_key' => $automation->action_key,
                'is_active' => $automation->is_active,
                'last_run' => $automation->runs->first()
                    ? [
                        'status' => $automation->runs->first()->status,
                        'executed_at' => $automation->runs->first()->executed_at?->toDateTimeString(),
                    ]
                    : null,
            ]);

        return Inertia::render('Admin/Automations/Index', [
            'automations' => $automations,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function show(Automation $automation)
    {
        $runs = $automation->runs()
            ->orderByDesc('executed_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($run) => [
                'id' => $run->id,
                'status' => $run->status,
                'executed_at' => $run->executed_at?->toDateTimeString(),
                'metadata' => $run->metadata,
            ]);

        return Inertia::render('Admin/Automations/Show', [
            'automation' => [
                'id' => $automation->id,
                'name' => $automation->name,
                'event_key' => $automation->event_key,
                'action_key' => $automation->action_key,
                'is_active' => $automation->is_active,
                'config' => $automation->config,
            ],
            'runs' => $runs,
        ]);
    }

    public function update(Request $request, Automation $automation, ActivityService $activity)
    {
        $data = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $automation->update([
            'is_active' => (bool) $data['is_active'],
        ]);

        $activity->log('automation_updated', [
            'actor' => $request->user(),
            'subject' => $automation,
            'description' => 'Automatizacion actualizada',
            'request' => $request,
        ]);

        return back()->with('success', 'Automatizacion actualizada correctamente.');
    }
}
