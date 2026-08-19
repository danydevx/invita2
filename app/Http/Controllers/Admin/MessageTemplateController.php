<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MessageTemplate;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class MessageTemplateController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $templates = MessageTemplate::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('key', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($template) => [
                'id' => $template->id,
                'key' => $template->key,
                'name' => $template->name,
                'is_active' => $template->is_active,
                'updated_at' => $template->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/MessageTemplates/Index', [
            'templates' => $templates,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/MessageTemplates/Create');
    }

    public function store(Request $request, ActivityService $activity)
    {
        $data = $this->validateTemplate($request);

        $template = MessageTemplate::create([
            'key' => trim($data['key']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'subject' => $data['subject'] ?? null,
            'content' => $data['content'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $activity->log('template_created', [
            'actor' => $request->user(),
            'subject' => $template,
            'description' => 'Plantilla creada',
            'request' => $request,
        ]);

        return redirect()->route('admin.message-templates.index')
            ->with('success', 'Plantilla creada correctamente.');
    }

    public function edit(MessageTemplate $template)
    {
        return Inertia::render('Admin/MessageTemplates/Edit', [
            'template' => [
                'id' => $template->id,
                'key' => $template->key,
                'name' => $template->name,
                'description' => $template->description,
                'subject' => $template->subject,
                'content' => $template->content,
                'is_active' => $template->is_active,
            ],
        ]);
    }

    public function update(Request $request, MessageTemplate $template, ActivityService $activity)
    {
        $data = $this->validateTemplate($request, $template->id);

        $template->update([
            'key' => trim($data['key']),
            'name' => trim($data['name']),
            'description' => $data['description'] ?? null,
            'subject' => $data['subject'] ?? null,
            'content' => $data['content'],
            'is_active' => (bool) ($data['is_active'] ?? true),
        ]);

        $activity->log('template_updated', [
            'actor' => $request->user(),
            'subject' => $template,
            'description' => 'Plantilla actualizada',
            'request' => $request,
        ]);

        return redirect()->route('admin.message-templates.index')
            ->with('success', 'Plantilla actualizada correctamente.');
    }

    public function destroy(MessageTemplate $template, ActivityService $activity)
    {
        $template->delete();

        $activity->log('template_deleted', [
            'actor' => request()->user(),
            'subject' => $template,
            'description' => 'Plantilla eliminada',
            'request' => request(),
        ]);

        return back()->with('success', 'Plantilla eliminada correctamente.');
    }

    private function validateTemplate(Request $request, ?int $templateId = null): array
    {
        return $request->validate([
            'key' => ['required', 'string', 'max:150', Rule::unique('message_templates', 'key')->ignore($templateId)],
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:500'],
            'subject' => ['nullable', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'is_active' => ['boolean'],
        ]);
    }
}
