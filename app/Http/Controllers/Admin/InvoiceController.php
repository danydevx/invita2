<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', '');
        $type = $request->input('type', '');
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');

        $invoices = Invoice::query()
            ->with(['user:id,name,email'])
            ->when($search !== '', function ($query) use ($search) {
                $needle = mb_strtolower($search);
                $query->where(function ($q) use ($needle) {
                    $q->whereRaw('LOWER(number) like ?', ['%'.$needle.'%'])
                        ->orWhereRaw('LOWER(provider_reference) like ?', ['%'.$needle.'%'])
                        ->orWhereHas('user', function ($userQuery) use ($needle) {
                            $userQuery->whereRaw('LOWER(name) like ?', ['%'.$needle.'%'])
                                ->orWhereRaw('LOWER(email) like ?', ['%'.$needle.'%']);
                        });
                });
            })
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->when($type !== '', fn ($query) => $query->where('type', $type))
            ->when($dateFrom !== '', fn ($query) => $query->whereDate('issued_at', '>=', $dateFrom))
            ->when($dateTo !== '', fn ($query) => $query->whereDate('issued_at', '<=', $dateTo))
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($invoice) => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'type' => $invoice->type,
                'status' => $invoice->status,
                'amount' => $invoice->amount,
                'currency' => $invoice->currency,
                'issued_at' => $invoice->issued_at?->toDateString(),
                'user' => $invoice->user ? [
                    'id' => $invoice->user->id,
                    'name' => $invoice->user->name,
                    'email' => $invoice->user->email,
                ] : null,
            ]);

        return Inertia::render('Admin/Invoices/Index', [
            'invoices' => $invoices,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'type' => $type,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
        ]);
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['user:id,name,email', 'payment:id,provider_reference', 'subscription:id,plan_id', 'subscription.plan:id,name']);

        return Inertia::render('Admin/Invoices/Show', [
            'invoice' => [
                'id' => $invoice->id,
                'number' => $invoice->number,
                'type' => $invoice->type,
                'status' => $invoice->status,
                'amount' => $invoice->amount,
                'currency' => $invoice->currency,
                'issued_at' => $invoice->issued_at?->toDateString(),
                'due_at' => $invoice->due_at?->toDateString(),
                'paid_at' => $invoice->paid_at?->toDateString(),
                'file_url' => $invoice->file_url,
                'file_path' => $invoice->file_path,
                'provider_reference' => $invoice->provider_reference,
                'user' => $invoice->user ? [
                    'id' => $invoice->user->id,
                    'name' => $invoice->user->name,
                    'email' => $invoice->user->email,
                ] : null,
                'payment' => $invoice->payment ? [
                    'id' => $invoice->payment->id,
                    'provider_reference' => $invoice->payment->provider_reference,
                ] : null,
                'plan' => $invoice->subscription?->plan ? [
                    'id' => $invoice->subscription->plan->id,
                    'name' => $invoice->subscription->plan->name,
                ] : null,
            ],
        ]);
    }

    public function download(Invoice $invoice)
    {
        if ($invoice->file_path && Storage::exists($invoice->file_path)) {
            return Storage::download($invoice->file_path);
        }

        if ($invoice->file_url) {
            return redirect()->away($invoice->file_url);
        }

        return back()->with('error', 'No hay archivo disponible para descargar.');
    }
}
