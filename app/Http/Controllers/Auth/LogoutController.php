<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use App\Services\SecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class LogoutController extends Controller
{
    public function destroy(Request $request, ActivityService $activity, SecurityService $security)
    {
        $user = $request->user();

        if ($user) {
            $activity->log('user_logout', [
                'user' => $user,
                'actor' => $user,
                'subject' => $user,
                'description' => 'Cierre de sesion',
                'request' => $request,
            ]);

            $security->log('logout', $user, $request, 'Logout');
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($request->header('X-Inertia')) {
            return Inertia::location(route('login'));
        }

        return redirect()->route('login');
    }
}
