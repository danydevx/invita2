<?php

namespace App\Http\Middleware;

use App\Models\LegalAcceptance;
use App\Models\LegalDocument;
use App\Services\ModuleService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLegalDocumentsAccepted
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Evita forzar aceptacion si el modulo legal esta desactivado.
        if (! app(ModuleService::class)->isEnabled('legal')) {
            return $next($request);
        }

        if (! $user || ! $user->is_active) {
            return $next($request);
        }

        if ($request->routeIs('legal.accept', 'legal.accept.store', 'logout')) {
            return $next($request);
        }

        $documents = LegalDocument::query()
            ->where('is_active', true)
            ->where('is_required', true)
            ->get();

        if ($documents->isEmpty()) {
            return $next($request);
        }

        $acceptances = LegalAcceptance::query()
            ->where('user_id', $user->id)
            ->orderByDesc('accepted_at')
            ->get()
            ->groupBy('legal_document_id')
            ->map(fn ($items) => $items->first());

        foreach ($documents as $document) {
            $acceptance = $acceptances->get($document->id);
            if (! $acceptance) {
                return redirect()->guest(route('legal.accept'))
                    ->with('error', 'Debes aceptar los documentos legales para continuar.');
            }

            if ($document->requires_reaccept && (int) $acceptance->version !== (int) $document->version) {
                return redirect()->guest(route('legal.accept'))
                    ->with('error', 'Debes aceptar los documentos legales actualizados para continuar.');
            }
        }

        return $next($request);
    }
}
