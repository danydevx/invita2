<?php

namespace Modules\Minisite\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Minisite\Models\BusinessMinisiteSetting;

class MinisiteController extends Controller
{
    public function index(Request $request, $business)
    {
        $setting = BusinessMinisiteSetting::where('listing_id', $business->id)->firstOrCreate([
            'listing_id' => $business->id,
        ]);

        return inertia('Admin/Minisite/Index', [
            'business' => $business,
            'setting' => $setting,
        ]);
    }

    public function update(Request $request, $business)
    {
        $setting = BusinessMinisiteSetting::where('listing_id', $business->id)->firstOrCreate([
            'listing_id' => $business->id,
        ]);

        $validated = $request->validate([
            'theme' => 'nullable|string|max:50',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'font_family' => 'nullable|string|max:100',
            'custom_css' => 'nullable|string',
        ]);

        $setting->update($validated);

        return redirect()->back()->with('success', 'Minisite settings updated.');
    }
}
