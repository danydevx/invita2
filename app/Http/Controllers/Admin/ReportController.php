<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
            'plan_id' => ['nullable', 'integer', Rule::exists('plans', 'id')],
            'payment_status' => ['nullable', 'string'],
            'ticket_status' => ['nullable', 'string'],
        ]);

        $dateFrom = $data['date_from'] ?? now()->subDays(30)->toDateString();
        $dateTo = $data['date_to'] ?? now()->toDateString();
        $planId = $data['plan_id'] ?? null;
        $paymentStatus = $data['payment_status'] ?? null;
        $ticketStatus = $data['ticket_status'] ?? null;

        $usersQuery = User::query()->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59']);
        $usersTotal = (clone $usersQuery)->count();
        $usersActive = (clone $usersQuery)->where('is_active', true)->count();
        $usersInactive = (clone $usersQuery)->where('is_active', false)->count();
        $usersVerified = (clone $usersQuery)->whereNotNull('email_verified_at')->count();
        $usersUnverified = (clone $usersQuery)->whereNull('email_verified_at')->count();

        $subscriptionsQuery = Subscription::query()->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59']);
        if ($planId) {
            $subscriptionsQuery->where('plan_id', $planId);
        }
        $subscriptionsTotal = (clone $subscriptionsQuery)->count();
        $subscriptionsActive = (clone $subscriptionsQuery)->where('status', 'active')->count();
        $subscriptionsTrial = (clone $subscriptionsQuery)->where('status', 'trial')->count();
        $subscriptionsCanceled = (clone $subscriptionsQuery)->where('status', 'canceled')->count();
        $subscriptionsExpired = (clone $subscriptionsQuery)
            ->whereNotNull('ends_at')
            ->where('ends_at', '<', now())
            ->count();

        $paymentsQuery = Payment::query()->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59']);
        if ($paymentStatus) {
            $paymentsQuery->where('status', $paymentStatus);
        }
        $paymentsTotal = (clone $paymentsQuery)->count();
        $paymentsPaid = (clone $paymentsQuery)->where('status', 'paid')->count();
        $paymentsFailed = (clone $paymentsQuery)->where('status', 'failed')->count();
        $paymentsAmount = (clone $paymentsQuery)->where('status', 'paid')->sum('amount');

        $paymentsByPlan = Payment::query()
            ->select('plan_id')
            ->selectRaw('sum(amount) as total')
            ->where('status', 'paid')
            ->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59'])
            ->when($planId, fn ($query) => $query->where('plan_id', $planId))
            ->groupBy('plan_id')
            ->get()
            ->map(function ($row) {
                return [
                    'plan_id' => $row->plan_id,
                    'total' => $row->total,
                ];
            });

        $plansById = Plan::query()->whereIn('id', $paymentsByPlan->pluck('plan_id'))->get(['id', 'name'])->keyBy('id');
        $paymentsByPlan = $paymentsByPlan->map(fn ($row) => [
            'plan' => $plansById[$row['plan_id']]?->name ?? 'Sin plan',
            'total' => $row['total'],
        ])->values();

        $ticketsQuery = SupportTicket::query()->whereBetween('created_at', [$dateFrom.' 00:00:00', $dateTo.' 23:59:59']);
        if ($ticketStatus) {
            $ticketsQuery->where('status', $ticketStatus);
        }
        $ticketsTotal = (clone $ticketsQuery)->count();
        $ticketsOpen = (clone $ticketsQuery)->where('status', 'open')->count();
        $ticketsPending = (clone $ticketsQuery)->where('status', 'pending')->count();
        $ticketsClosed = (clone $ticketsQuery)->where('status', 'closed')->count();

        return Inertia::render('Admin/Reports/Index', [
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'plan_id' => $planId,
                'payment_status' => $paymentStatus,
                'ticket_status' => $ticketStatus,
            ],
            'plans' => Plan::query()->orderByRaw('sort_order is null, sort_order asc')->orderBy('id')->get(['id', 'name']),
            'reports' => [
                'users' => [
                    'total' => $usersTotal,
                    'active' => $usersActive,
                    'inactive' => $usersInactive,
                    'verified' => $usersVerified,
                    'unverified' => $usersUnverified,
                ],
                'subscriptions' => [
                    'total' => $subscriptionsTotal,
                    'active' => $subscriptionsActive,
                    'trial' => $subscriptionsTrial,
                    'expired' => $subscriptionsExpired,
                    'canceled' => $subscriptionsCanceled,
                ],
                'payments' => [
                    'total' => $paymentsTotal,
                    'paid' => $paymentsPaid,
                    'failed' => $paymentsFailed,
                    'amount_total' => $paymentsAmount,
                    'by_plan' => $paymentsByPlan,
                ],
                'tickets' => [
                    'total' => $ticketsTotal,
                    'open' => $ticketsOpen,
                    'pending' => $ticketsPending,
                    'closed' => $ticketsClosed,
                ],
            ],
        ]);
    }
}
