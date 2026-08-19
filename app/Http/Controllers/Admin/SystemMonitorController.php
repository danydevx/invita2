<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\SystemError;
use App\Models\User;
use App\Models\WebhookDelivery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SystemMonitorController extends Controller
{
    public function index(Request $request)
    {
        $now = now();

        $usersTotal = User::query()->count();
        $usersLast7Days = User::query()->where('created_at', '>=', $now->copy()->subDays(7))->count();

        $subscriptionsActive = Subscription::query()->where('status', 'active')->count();
        $subscriptionsTrial = Subscription::query()->where('status', 'trial')->count();
        $subscriptionsCanceled = Subscription::query()->where('status', 'canceled')->count();

        $paymentsToday = Payment::query()->whereDate('created_at', $now->toDateString())->count();
        $paymentsFailedToday = Payment::query()->whereDate('created_at', $now->toDateString())->where('status', 'failed')->count();
        $revenueMonth = Payment::query()
            ->where('status', 'paid')
            ->whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('amount');

        $jobsPending = (int) DB::table('jobs')->count();
        $jobsFailed = (int) DB::table('failed_jobs')->count();

        $errorsLast24h = SystemError::query()->where('created_at', '>=', $now->copy()->subDay())->count();
        $errorsUnresolved = SystemError::query()->where('is_resolved', false)->count();

        $webhookFailures = WebhookDelivery::query()
            ->whereNotNull('failed_at')
            ->orderByDesc('failed_at')
            ->limit(10)
            ->get(['id', 'event', 'failed_at', 'response_status', 'error_message']);

        $recentErrors = SystemError::query()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'type', 'message', 'created_at', 'is_resolved']);

        $recentActivity = Activity::query()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get(['id', 'type', 'description', 'created_at']);

        $ticketsOpen = SupportTicket::query()->where('status', 'open')->count();
        $ticketsPending = SupportTicket::query()->where('status', 'pending')->count();

        return Inertia::render('Admin/SystemMonitor/Index', [
            'metrics' => [
                'users_total' => $usersTotal,
                'users_last_7_days' => $usersLast7Days,
                'subscriptions_active' => $subscriptionsActive,
                'subscriptions_trial' => $subscriptionsTrial,
                'subscriptions_canceled' => $subscriptionsCanceled,
                'payments_today' => $paymentsToday,
                'payments_failed_today' => $paymentsFailedToday,
                'revenue_month' => (float) $revenueMonth,
                'jobs_pending' => $jobsPending,
                'jobs_failed' => $jobsFailed,
                'errors_last_24h' => $errorsLast24h,
                'errors_unresolved' => $errorsUnresolved,
                'tickets_open' => $ticketsOpen,
                'tickets_pending' => $ticketsPending,
            ],
            'recentErrors' => $recentErrors->map(fn ($error) => [
                'id' => $error->id,
                'type' => $error->type,
                'message' => $error->message,
                'created_at' => $error->created_at?->toDateTimeString(),
                'is_resolved' => $error->is_resolved,
            ]),
            'webhookFailures' => $webhookFailures->map(fn ($delivery) => [
                'id' => $delivery->id,
                'event' => $delivery->event,
                'failed_at' => $delivery->failed_at?->toDateTimeString(),
                'response_status' => $delivery->response_status,
                'error_message' => $delivery->error_message,
            ]),
            'recentActivity' => $recentActivity->map(fn ($activity) => [
                'id' => $activity->id,
                'type' => $activity->type,
                'description' => $activity->description,
                'created_at' => $activity->created_at?->toDateTimeString(),
            ]),
        ]);
    }
}
