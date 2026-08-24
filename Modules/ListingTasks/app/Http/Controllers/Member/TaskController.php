<?php

namespace Modules\ListingTasks\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingTasks\Models\ListingTask;

class TaskController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingTask::class, $business]);

        $tasks = $business->tasks()
            ->whereNull('archived_at')
            ->orderBy('status')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $groupedTasks = [
            'todo' => $tasks->where('status', 'todo')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->values(),
            'revision' => $tasks->where('status', 'revision')->values(),
            'done' => $tasks->where('status', 'done')->values(),
        ];

        $completedTasks = $business->tasks()
            ->where('status', 'done')
            ->orderByDesc('completed_at')
            ->limit(50)
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'description' => $t->description,
                'completed_at' => $t->completed_at?->toIso8601String(),
                'archived_at' => $t->archived_at?->toIso8601String(),
                'created_at' => $t->created_at->toIso8601String(),
            ]);

        return Inertia::render('Member/Tasks/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'tasks' => $groupedTasks,
            'completedTasks' => $completedTasks,
        ]);
    }

    public function store(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingTask::class, $business]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,revision,done'],
        ]);

        $maxOrder = $business->tasks()->where('status', $data['status'])->max('sort_order') ?? 0;

        $taskData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'status' => $data['status'],
            'sort_order' => $maxOrder + 1,
        ];

        if ($data['status'] === 'done') {
            $taskData['completed_at'] = now();
        }

        $task = $business->tasks()->create($taskData);

        return redirect()->back()->with('success', 'Tarea creada correctamente.');
    }

    public function update(Request $request, Listing $business, ListingTask $task)
    {
        $this->authorize('update', [ListingTask::class, $task]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,revision,done'],
        ]);

        $oldStatus = $task->status;
        $newStatus = $data['status'];

        if ($oldStatus !== $newStatus) {
            $maxOrder = $business->tasks()->where('status', $newStatus)->max('sort_order') ?? 0;
            $data['sort_order'] = $maxOrder + 1;

            if ($newStatus === 'done') {
                $data['completed_at'] = now();
            } else {
                $data['completed_at'] = null;
            }
        }

        $task->update($data);

        return redirect()->back()->with('success', 'Tarea actualizada correctamente.');
    }

    public function archive(Request $request, Listing $business, ListingTask $task)
    {
        $this->authorize('update', [ListingTask::class, $task]);

        $task->update(['archived_at' => now()]);

        return redirect()->back()->with('success', 'Tarea archivada.');
    }

    public function destroy(Request $request, Listing $business, ListingTask $task)
    {
        $this->authorize('delete', [ListingTask::class, $task]);

        $task->delete();

        return redirect()->back()->with('success', 'Tarea eliminada correctamente.');
    }

    public function reorder(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['integer', \Illuminate\Validation\Rule::exists('listing_tasks', 'id')->where('listing_id', $business->id)->whereNull('archived_at')],
            'items.*.status' => ['required', 'string', 'in:todo,in_progress,revision,done'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        $allowedStatuses = ['todo', 'in_progress', 'revision', 'done'];

        \DB::transaction(function () use ($data, $allowedStatuses) {
            foreach ($data['items'] as $item) {
                $status = in_array($item['status'], $allowedStatuses) ? $item['status'] : 'todo';
                $updateData = [
                    'status' => $status,
                    'sort_order' => (int) $item['sort_order'],
                ];

                if ($item['status'] === 'done') {
                    $updateData['completed_at'] = now();
                } else {
                    $updateData['completed_at'] = null;
                }

                ListingTask::where('id', (int) $item['id'])->update($updateData);
            }
        });

        return back(303);
    }
}