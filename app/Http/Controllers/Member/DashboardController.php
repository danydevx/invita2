<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\ListingAppointments\Models\ListingAppointment;
use Modules\Listings\Models\Listing;
use Modules\ListingLeads\Models\ListingLead;
use Modules\ListingModules\Models\ListingModule;
use Modules\ListingPromotions\Models\ListingPromotion;
use Modules\ListingReviews\Models\ListingReview;

class DashboardController extends Controller
{
    private const CONTENT_MODULES = [
        'services',
        'products',
        'gallery',
        'leads',
        'appointments',
        'promotions',
        'reviews',
        'team_members',
        'packages',
        'restaurant_menu',
        'properties',
    ];

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasVerifiedEmail() && $user->listings()->doesntExist()) {
            $slug = $this->generateUniqueSlug();
            Listing::create([
                'user_id' => $user->id,
                'name' => substr($slug, 0, 12),
                'slug' => $slug,
                'listing_type' => 'generic',
                'is_active' => true,
                'is_published' => false,
            ]);
        }

        $listings = $user->listings()
            ->select('id', 'name', 'description', 'is_published')
            ->orderBy('name')
            ->get()
            ->map(fn ($biz) => [
                'id' => $biz->id,
                'name' => $biz->name,
                'description' => $biz->description,
                'is_published' => $biz->is_published,
            ]);

        $businessCount = $listings->count();

        $moduleStats = [];
        if ($businessCount > 0) {
            $primaryListingId = $listings->first()['id'];

            $enabledModules = ListingModule::where('listing_id', $primaryListingId)
                ->enabled()
                ->pluck('module_key')
                ->toArray();

            $contentModules = array_intersect($enabledModules, self::CONTENT_MODULES);

            $businessIds = $listings->pluck('id');

            $moduleStats = $this->getModuleStats($contentModules, $businessIds);
        }

        return Inertia::render('Member/Dashboard', [
            'businessCount' => $businessCount,
            'listings' => $listings,
            'moduleStats' => $moduleStats,
        ]);
    }

    private function getModuleStats(array $enabledModules, $businessIds): array
    {
        $stats = [];

        if (empty($enabledModules)) {
            return $stats;
        }

        $modelMap = [
            'services' => null,
            'products' => null,
            'gallery' => null,
            'leads' => ListingLead::class,
            'appointments' => ListingAppointment::class,
            'promotions' => ListingPromotion::class,
            'reviews' => ListingReview::class,
            'team_members' => null,
            'packages' => null,
            'restaurant_menu' => null,
            'properties' => null,
        ];

        $iconMap = [
            'services' => 'bi-briefcase',
            'products' => 'bi-cart',
            'gallery' => 'bi-images',
            'leads' => 'bi-people',
            'appointments' => 'bi-calendar-event',
            'promotions' => 'bi-megaphone',
            'reviews' => 'bi-star',
            'team_members' => 'bi-person-badge',
            'packages' => 'bi-box-seam',
            'restaurant_menu' => 'bi-cup-hot',
            'properties' => 'bi-house',
        ];

        $labelMap = [
            'services' => 'Servicios',
            'products' => 'Productos',
            'gallery' => 'Galería',
            'leads' => 'Leads',
            'appointments' => 'Citas',
            'promotions' => 'Promociones',
            'reviews' => 'Reseñas',
            'team_members' => 'Equipo',
            'packages' => 'Paquetes',
            'restaurant_menu' => 'Menú',
            'properties' => 'Propiedades',
        ];

        foreach ($enabledModules as $moduleKey) {
            $modelClass = $modelMap[$moduleKey] ?? null;
            $icon = $iconMap[$moduleKey] ?? 'bi-grid';
            $label = $labelMap[$moduleKey] ?? ucfirst(str_replace('_', ' ', $moduleKey));

            $count = 0;
            if ($modelClass && count($businessIds) > 0) {
                $count = $modelClass::whereIn('listing_id', $businessIds)->count();
            }

            $stats[] = [
                'key' => $moduleKey,
                'label' => $label,
                'icon' => $icon,
                'count' => $count,
            ];
        }

        return $stats;
    }

    private function generateUniqueSlug(): string
    {
        do {
            $slug = substr(hash('sha256', Str::random(16)), 0, 12);
        } while (Listing::where('slug', $slug)->exists());

        return $slug;
    }
}
