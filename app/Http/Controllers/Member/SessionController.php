<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $currentId = $request->session()->getId();

        $sessions = DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('last_activity')
            ->get()
            ->map(function ($session) use ($currentId) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $this->truncateAgent($session->user_agent),
                    'last_activity' => $this->formatActivity($session->last_activity),
                    'is_current' => $session->id === $currentId,
                ];
            });

        return Inertia::render('Member/Sessions/Index', [
            'sessions' => $sessions,
        ]);
    }

    public function destroy(Request $request, string $session, ActivityService $activity, SecurityService $security)
    {
        $currentId = $request->session()->getId();
        if ($session === $currentId) {
            return back()->with('error', 'No puedes cerrar la sesion actual desde aqui.');
        }

        $deleted = DB::table('sessions')
            ->where('id', $session)
            ->where('user_id', $request->user()->id)
            ->delete();

        if (! $deleted) {
            return back()->with('error', 'No tiene permiso para cerrar esta sesion.');
        }

        $activity->log('session_terminated', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'description' => 'Sesion cerrada por el usuario',
            'request' => $request,
        ]);

        $security->log('session_terminated', $request->user(), $request, 'Sesion cerrada');

        return back()->with('success', 'La sesion fue cerrada correctamente.');
    }

    public function destroyOthers(Request $request, ActivityService $activity, SecurityService $security)
    {
        $currentId = $request->session()->getId();

        DB::table('sessions')
            ->where('user_id', $request->user()->id)
            ->where('id', '!=', $currentId)
            ->delete();

        $activity->log('other_sessions_terminated', [
            'user' => $request->user(),
            'actor' => $request->user(),
            'description' => 'Cierre de otras sesiones',
            'request' => $request,
        ]);

        $security->log('other_sessions_terminated', $request->user(), $request, 'Otras sesiones cerradas');

        return back()->with('success', 'Las demas sesiones fueron cerradas correctamente.');
    }

    private function formatActivity(int $timestamp): string
    {
        return now()->setTimestamp($timestamp)->toDateTimeString();
    }

    private function truncateAgent(?string $agent): string
    {
        if (! $agent) {
            return '-';
        }

        return mb_substr($agent, 0, 80);
    }
}
