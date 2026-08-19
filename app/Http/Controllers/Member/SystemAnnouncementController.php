<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\SystemAnnouncement;
use App\Services\ActivityService;
use App\Services\SystemAnnouncementService;
use Illuminate\Http\Request;

class SystemAnnouncementController extends Controller
{
    public function active(Request $request, SystemAnnouncementService $announcements)
    {
        $items = $announcements->activeForUser($request->user())
            ->map(fn ($announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'message' => $announcement->message,
                'type' => $announcement->type,
                'priority' => $announcement->priority,
                'dismissible' => $announcement->dismissible,
                'action_label' => $announcement->action_label,
                'action_url' => $announcement->action_url,
            ])
            ->values();

        return response()->json([
            'announcements' => $items,
        ]);
    }

    public function dismiss(Request $request, SystemAnnouncement $announcement, SystemAnnouncementService $announcements, ActivityService $activity)
    {
        $user = $request->user();
        if (! $announcements->dismiss($user, $announcement)) {
            return back()->with('error', 'No se pudo actualizar la preferencia seleccionada.');
        }

        $activity->log('announcement_dismissed', [
            'actor' => $user,
            'subject' => $announcement,
            'description' => 'Anuncio descartado',
            'request' => $request,
        ]);

        return back()->with('success', 'Anuncio descartado.');
    }
}
