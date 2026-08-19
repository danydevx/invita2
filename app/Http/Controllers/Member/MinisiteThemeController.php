<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Businesses\Models\Business;
use App\Models\MinisiteTheme;
use Illuminate\Support\Facades\Auth;

class MinisiteThemeController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $themes = MinisiteTheme::where('is_active', true)->orderBy('name')->get();

        $business->load('minisiteTheme');

        return inertia('Member/MinisiteThemes/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'theme' => $business->minisiteTheme ? [
                    'id' => $business->minisiteTheme->id,
                    'name' => $business->minisiteTheme->name,
                    'slug' => $business->minisiteTheme->slug,
                ] : null,
            ],
            'themes' => $themes,
        ]);
    }

    public function update(Request $request, Business $business, MinisiteTheme $theme)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($theme->is_active, 403, 'Este theme no está disponible.');

        $business->update(['minisite_theme_id' => $theme->id]);

        return redirect()->back()->with('success', 'Theme actualizado exitosamente.');
    }
}
