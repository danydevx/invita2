<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::query()
            ->where('user_id', $request->user()->id)
            ->with('plan:id,name')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($payment) => [
                'id' => $payment->id,
                'amount' => $payment->amount,
                'currency' => $payment->currency,
                'status' => $payment->status,
                'plan' => $payment->plan ? [
                    'id' => $payment->plan->id,
                    'name' => $payment->plan->name,
                ] : null,
                'provider_reference' => $payment->provider_reference,
                'paid_at' => $payment->paid_at?->toDateString(),
                'created_at' => $payment->created_at?->toDateString(),
            ]);

        return Inertia::render('Member/Payments/Index', [
            'payments' => $payments,
        ]);
    }
}
