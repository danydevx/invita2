<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LegalAcceptance;
use App\Models\LegalDocument;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LegalAcceptanceController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $documents = LegalDocument::query()
            ->where('is_active', true)
            ->where('is_required', true)
            ->orderBy('title')
            ->get();

        if ($documents->isEmpty()) {
            return redirect()->intended('/member');
        }

        $acceptances = LegalAcceptance::query()
            ->where('user_id', $user->id)
            ->orderByDesc('accepted_at')
            ->get()
            ->groupBy('legal_document_id')
            ->map(fn ($items) => $items->first());

        $pendingDocuments = $documents->filter(function ($document) use ($acceptances) {
            $acceptance = $acceptances->get($document->id);
            if (! $acceptance) {
                return true;
            }

            if ($document->requires_reaccept && (int) $acceptance->version !== (int) $document->version) {
                return true;
            }

            return false;
        })->values();

        if ($pendingDocuments->isEmpty()) {
            return redirect()->intended('/member');
        }

        return Inertia::render('Auth/Legal/Accept', [
            'documents' => $pendingDocuments->map(fn ($document) => [
                'id' => $document->id,
                'title' => $document->title,
                'content' => $document->content,
                'version' => $document->version,
                'key' => $document->key,
            ]),
        ]);
    }

    public function store(Request $request, ActivityService $activity)
    {
        $user = $request->user();

        $data = $request->validate([
            'documents' => ['required', 'array'],
            'documents.*' => ['integer', 'exists:legal_documents,id'],
        ]);

        $documents = LegalDocument::query()
            ->where('is_active', true)
            ->where('is_required', true)
            ->whereIn('id', $data['documents'])
            ->get();

        $allRequired = LegalDocument::query()
            ->where('is_active', true)
            ->where('is_required', true)
            ->get();

        $acceptances = LegalAcceptance::query()
            ->where('user_id', $user->id)
            ->orderByDesc('accepted_at')
            ->get()
            ->groupBy('legal_document_id')
            ->map(fn ($items) => $items->first());

        $pendingIds = $allRequired->filter(function ($document) use ($acceptances) {
            $acceptance = $acceptances->get($document->id);
            if (! $acceptance) {
                return true;
            }

            if ($document->requires_reaccept && (int) $acceptance->version !== (int) $document->version) {
                return true;
            }

            return false;
        })->pluck('id')->values()->all();

        $missing = array_diff($pendingIds, $data['documents']);
        if (! empty($missing)) {
            return back()->withErrors([
                'documents' => 'Debes aceptar todos los documentos pendientes.',
            ]);
        }

        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        if ($userAgent !== null) {
            $userAgent = substr($userAgent, 0, 500);
        }

        foreach ($documents as $document) {
            LegalAcceptance::firstOrCreate([
                'legal_document_id' => $document->id,
                'user_id' => $user->id,
                'version' => $document->version,
            ], [
                'accepted_at' => now(),
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);
        }

        $activity->log('legal_documents_accepted', [
            'actor' => $user,
            'subject' => $user,
            'description' => 'Documentos legales aceptados',
            'request' => $request,
            'meta' => [
                'document_ids' => $documents->pluck('id')->values()->all(),
            ],
        ]);

        return redirect()->intended('/member')->with('success', 'Documentos legales aceptados.');
    }
}
