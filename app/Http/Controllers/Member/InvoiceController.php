<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $invoices = Invoice::query()
            ->where('user_id', $request->user()->id)
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
                'paid_at' => $invoice->paid_at?->toDateString(),
                'provider_reference' => $invoice->provider_reference,
            ]);

        return Inertia::render('Member/Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    public function show(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        return Inertia::render('Member/Invoices/Show', [
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
                'provider_reference' => $invoice->provider_reference,
            ],
        ]);
    }

    public function download(Request $request, Invoice $invoice)
    {
        if ($invoice->user_id !== $request->user()->id) {
            abort(403);
        }

        if ($invoice->file_path && Storage::exists($invoice->file_path)) {
            return Storage::download($invoice->file_path);
        }

        if ($invoice->file_url) {
            return redirect()->away($invoice->file_url);
        }

        return back()->with('error', 'No hay archivo disponible para descargar.');
    }
}
