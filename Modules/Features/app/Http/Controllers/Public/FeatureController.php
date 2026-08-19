<?php

namespace Modules\Features\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Businesses\Models\Business;
use Modules\Features\Models\BusinessFeature;

class FeatureController extends Controller
{
    public function index(Business $business)
    {
        if (!$business->is_active || !$business->is_published) {
            abort(404);
        }

        $features = BusinessFeature::with(['feature', 'location'])
            ->where('business_id', $business->id)
            ->where('is_active', true)
            ->get()
            ->sortBy(function ($bf) {
                return $bf->feature->sort_order ?? 0;
            })
            ->values()
            ->map(function ($bf) {
                return [
                    'id' => $bf->id,
                    'title' => $bf->feature->title,
                    'description' => $bf->feature->description,
                    'icon' => $bf->feature->icon,
                    'image_path' => $bf->feature->image_path,
                    'location_name' => $bf->location?->name,
                    'location_id' => $bf->location_id,
                ];
            });

        return response()->json([
            'business_id' => $business->id,
            'business_name' => $business->name,
            'features' => $features,
        ]);
    }
}
