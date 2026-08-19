<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SupportTicket;
use App\Models\SystemError;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $stats = [
            'totalUsers' => User::query()->count(),
            'activeSubscriptions' => Subscription::query()->where('status', 'active')->count(),
            'openTickets' => SupportTicket::query()->whereNotIn('status', ['closed', 'resolved'])->count(),
            'recentErrors' => SystemError::query()->where('created_at', '>=', now()->subDays(7))->count(),
        ];

        $recentActivity = Activity::query()
            ->latest()
            ->limit(10)
            ->get(['type', 'description', 'created_at', 'user_id'])
            ->map(fn ($log) => [
                'type' => $log->type,
                'description' => $log->description,
                'created_at' => $log->created_at?->toDateTimeString(),
            ]);

        $recentErrors = SystemError::query()
            ->latest()
            ->limit(5)
            ->get(['id', 'type', 'message', 'created_at']);

        $openTickets = SupportTicket::query()
            ->whereNotIn('status', ['closed', 'resolved'])
            ->with('user:id,name,email')
            ->latest()
            ->limit(5)
            ->get(['id', 'subject', 'status', 'user_id', 'created_at']);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentActivity' => $recentActivity,
            'recentErrors' => $recentErrors,
            'openTickets' => $openTickets,
        ]);
    }
}
