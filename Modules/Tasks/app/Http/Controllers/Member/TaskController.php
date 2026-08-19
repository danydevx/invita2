<?php

namespace Modules\Tasks\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Tasks\Models\BusinessTask;

class TaskController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('viewAny', [BusinessTask::class, $business]);

        $tasks = $business->tasks()
            ->orderBy('status')
            ->orderBy('sort_order')
            ->orderByDesc('id')
            ->get();

        $groupedTasks = [
            'todo' => $tasks->where('status', 'todo')->values(),
            'in_progress' => $tasks->where('status', 'in_progress')->values(),
            'done' => $tasks->where('status', 'done')->values(),
        ];

        $completedTasks = $business->tasks()
            ->where('status', 'done')
            ->orderByDesc('completed_at')
            ->limit(20)
            ->get()
            ->map(fn ($t) => [
                'id' => $t->id,
                'title' => $t->title,
                'description' => $t->description,
                'completed_at' => $t->completed_at?->toIso8601String(),
                'created_at' => $t->created_at->toIso8601String(),
            ]);

        return Inertia::render('Member/Tasks/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'tasks' => $groupedTasks,
            'completedTasks' => $completedTasks,
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $this->authorize('create', [BusinessTask::class, $business]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,done'],
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

    public function update(Request $request, Business $business, BusinessTask $task)
    {
        $this->authorize('update', [BusinessTask::class, $task]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:todo,in_progress,done'],
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

    public function destroy(Request $request, Business $business, BusinessTask $task)
    {
        $this->authorize('delete', [BusinessTask::class, $task]);

        $task->delete();

        return redirect()->back()->with('success', 'Tarea eliminada correctamente.');
    }

    public function reorder(Request $request, Business $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'items' => ['required', 'array'],
            'items.*.id' => ['integer', \Illuminate\Validation\Rule::exists('business_tasks', 'id')->where('business_id', $business->id)],
            'items.*.status' => ['required', 'in:todo,in_progress,done'],
            'items.*.sort_order' => ['required', 'integer', 'min:0'],
        ]);

        \DB::transaction(function () use ($data) {
            foreach ($data['items'] as $item) {
                $updateData = [
                    'status' => $item['status'],
                    'sort_order' => $item['sort_order'],
                ];

                if ($item['status'] === 'done') {
                    $updateData['completed_at'] = now();
                } else {
                    $updateData['completed_at'] = null;
                }

                BusinessTask::where('id', $item['id'])->update($updateData);
            }
        });

        return back(303);
    }
}
