<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function complete(Request $request)
    {
        $user = $request->user();

        if (! $user->onboarding_completed_at) {
            $user->forceFill([
                'onboarding_completed_at' => now(),
            ])->save();
        }

        return back()->with('success', 'Onboarding completado correctamente.');
    }
}
