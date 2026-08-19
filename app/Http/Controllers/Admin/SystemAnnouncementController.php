<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemAnnouncement;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SystemAnnouncementController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', '');

        $announcements = SystemAnnouncement::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('message', 'like', "%{$search}%");
                });
            })
            ->when($status !== '', fn ($query) => $query->where('is_active', $status === 'active'))
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'type' => $announcement->type,
                'audience' => $announcement->audience,
                'is_active' => $announcement->is_active,
                'starts_at' => $announcement->starts_at?->toDateTimeString(),
                'ends_at' => $announcement->ends_at?->toDateTimeString(),
                'priority' => $announcement->priority,
                'dismissible' => $announcement->dismissible,
                'created_at' => $announcement->created_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/Announcements/Index', [
            'announcements' => $announcements,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Announcements/Create');
    }

    public function store(Request $request, ActivityService $activity)
    {
        $data = $this->validateAnnouncement($request);

        $announcement = SystemAnnouncement::create([
            'title' => trim($data['title']),
            'message' => $data['message'],
            'type' => $data['type'],
            'audience' => $data['audience'],
            'is_active' => (bool) ($data['is_active'] ?? false),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'dismissible' => (bool) ($data['dismissible'] ?? true),
            'priority' => $data['priority'] ?? null,
            'action_label' => $data['action_label'] ?? null,
            'action_url' => $data['action_url'] ?? null,
            'created_by_user_id' => $request->user()->id,
        ]);

        $activity->log('announcement_created', [
            'actor' => $request->user(),
            'subject' => $announcement,
            'description' => 'Anuncio creado',
            'request' => $request,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Anuncio creado correctamente.');
    }

    public function edit(SystemAnnouncement $announcement)
    {
        return Inertia::render('Admin/Announcements/Edit', [
            'announcement' => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'message' => $announcement->message,
                'type' => $announcement->type,
                'audience' => $announcement->audience,
                'is_active' => $announcement->is_active,
                'starts_at' => $announcement->starts_at?->toDateTimeString(),
                'ends_at' => $announcement->ends_at?->toDateTimeString(),
                'dismissible' => $announcement->dismissible,
                'priority' => $announcement->priority,
                'action_label' => $announcement->action_label,
                'action_url' => $announcement->action_url,
            ],
        ]);
    }

    public function update(Request $request, SystemAnnouncement $announcement, ActivityService $activity)
    {
        $data = $this->validateAnnouncement($request);

        $announcement->update([
            'title' => trim($data['title']),
            'message' => $data['message'],
            'type' => $data['type'],
            'audience' => $data['audience'],
            'is_active' => (bool) ($data['is_active'] ?? false),
            'starts_at' => $data['starts_at'] ?? null,
            'ends_at' => $data['ends_at'] ?? null,
            'dismissible' => (bool) ($data['dismissible'] ?? true),
            'priority' => $data['priority'] ?? null,
            'action_label' => $data['action_label'] ?? null,
            'action_url' => $data['action_url'] ?? null,
        ]);

        $activity->log('announcement_updated', [
            'actor' => $request->user(),
            'subject' => $announcement,
            'description' => 'Anuncio actualizado',
            'request' => $request,
        ]);

        return redirect()->route('admin.announcements.index')
            ->with('success', 'Anuncio actualizado correctamente.');
    }

    public function destroy(SystemAnnouncement $announcement, ActivityService $activity)
    {
        $announcement->delete();

        $activity->log('announcement_deleted', [
            'actor' => request()->user(),
            'subject' => $announcement,
            'description' => 'Anuncio eliminado',
            'request' => request(),
        ]);

        return back()->with('success', 'Anuncio eliminado correctamente.');
    }

    private function validateAnnouncement(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'message' => ['required', 'string', 'max:5000'],
            'type' => ['required', Rule::in(['info', 'success', 'warning', 'danger'])],
            'audience' => ['required', Rule::in(['all', 'members', 'admins'])],
            'is_active' => ['boolean'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'dismissible' => ['boolean'],
            'priority' => ['nullable', Rule::in(['low', 'normal', 'high', 'critical'])],
            'action_label' => ['nullable', 'string', 'max:100'],
            'action_url' => ['nullable', 'url', 'max:500'],
        ]);
    }
}
