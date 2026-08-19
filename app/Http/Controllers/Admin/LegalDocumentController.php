<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LegalDocument;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class LegalDocumentController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));

        $documents = LegalDocument::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('key', 'like', "%{$search}%")
                        ->orWhere('title', 'like', "%{$search}%");
                });
            })
            ->withCount('acceptances')
            ->orderBy('title')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($document) => [
                'id' => $document->id,
                'key' => $document->key,
                'title' => $document->title,
                'version' => $document->version,
                'is_active' => $document->is_active,
                'is_required' => $document->is_required,
                'requires_reaccept' => $document->requires_reaccept,
                'acceptances_count' => $document->acceptances_count,
                'updated_at' => $document->updated_at?->toDateTimeString(),
            ]);

        return Inertia::render('Admin/LegalDocuments/Index', [
            'documents' => $documents,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/LegalDocuments/Create');
    }

    public function store(Request $request, ActivityService $activity)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:150', 'unique:legal_documents,key'],
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'is_active' => ['boolean'],
            'is_required' => ['boolean'],
            'requires_reaccept' => ['boolean'],
        ]);

        $document = LegalDocument::create([
            'key' => trim($data['key']),
            'title' => trim($data['title']),
            'content' => $data['content'],
            'version' => 1,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_required' => (bool) ($data['is_required'] ?? true),
            'requires_reaccept' => (bool) ($data['requires_reaccept'] ?? true),
        ]);

        $activity->log('legal_document_created', [
            'actor' => $request->user(),
            'subject' => $document,
            'description' => 'Documento legal creado',
            'request' => $request,
        ]);

        return redirect()->route('admin.legal-documents.index');
    }

    public function show(LegalDocument $document)
    {
        $acceptances = $document->acceptances()
            ->with('user:id,name,email')
            ->orderByDesc('accepted_at')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($acceptance) => [
                'id' => $acceptance->id,
                'user' => $acceptance->user ? [
                    'id' => $acceptance->user->id,
                    'name' => $acceptance->user->name,
                    'email' => $acceptance->user->email,
                ] : null,
                'version' => $acceptance->version,
                'accepted_at' => $acceptance->accepted_at?->toDateTimeString(),
                'ip_address' => $acceptance->ip_address,
            ]);

        return Inertia::render('Admin/LegalDocuments/Show', [
            'document' => [
                'id' => $document->id,
                'key' => $document->key,
                'title' => $document->title,
                'content' => $document->content,
                'version' => $document->version,
                'is_active' => $document->is_active,
                'is_required' => $document->is_required,
                'requires_reaccept' => $document->requires_reaccept,
                'updated_at' => $document->updated_at?->toDateTimeString(),
            ],
            'acceptances' => $acceptances,
        ]);
    }

    public function edit(LegalDocument $document)
    {
        return Inertia::render('Admin/LegalDocuments/Edit', [
            'document' => [
                'id' => $document->id,
                'key' => $document->key,
                'title' => $document->title,
                'content' => $document->content,
                'version' => $document->version,
                'is_active' => $document->is_active,
                'is_required' => $document->is_required,
                'requires_reaccept' => $document->requires_reaccept,
            ],
        ]);
    }

    public function update(Request $request, LegalDocument $document, ActivityService $activity)
    {
        $data = $request->validate([
            'key' => ['required', 'string', 'max:150', Rule::unique('legal_documents', 'key')->ignore($document->id)],
            'title' => ['required', 'string', 'max:200'],
            'content' => ['required', 'string'],
            'is_active' => ['boolean'],
            'is_required' => ['boolean'],
            'requires_reaccept' => ['boolean'],
            'bump_version' => ['boolean'],
        ]);

        $bumpVersion = (bool) ($data['bump_version'] ?? false);

        $document->update([
            'key' => trim($data['key']),
            'title' => trim($data['title']),
            'content' => $data['content'],
            'version' => $bumpVersion ? $document->version + 1 : $document->version,
            'is_active' => (bool) ($data['is_active'] ?? true),
            'is_required' => (bool) ($data['is_required'] ?? true),
            'requires_reaccept' => (bool) ($data['requires_reaccept'] ?? true),
        ]);

        $activity->log('legal_document_updated', [
            'actor' => $request->user(),
            'subject' => $document,
            'description' => 'Documento legal actualizado',
            'request' => $request,
        ]);

        return redirect()->route('admin.legal-documents.index');
    }
}
