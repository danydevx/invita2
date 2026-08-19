<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $notifications = UserNotification::query()
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($notification) => [
                'id' => $notification->id,
                'type' => $notification->type,
                'title' => $notification->title,
                'message' => $notification->message,
                'url' => $notification->url,
                'is_read' => (bool) $notification->is_read,
                'read_at' => $notification->read_at?->toDateTimeString(),
                'created_at' => $notification->created_at?->toDateTimeString(),
            ]);

        $unreadCount = UserNotification::query()
            ->where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return Inertia::render('Member/Notifications/Index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function unreadCount(Request $request)
    {
        $count = UserNotification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'count' => $count,
        ]);
    }

    public function markAsRead(Request $request, int $notification)
    {
        $user = $request->user();

        $record = UserNotification::query()
            ->where('id', $notification)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if (! $record->is_read) {
            $record->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return back()->with('success', 'Notificacion marcada como leida.');
    }

    public function markAllAsRead(Request $request)
    {
        UserNotification::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'Notificaciones marcadas como leidas.');
    }
}
