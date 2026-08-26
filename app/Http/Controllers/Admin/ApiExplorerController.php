<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BusinessListResource;
use App\Http\Resources\Api\V1\BusinessResource;
use App\Http\Resources\Api\V1\UserListResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Resources\Api\V1\IndustryResource;
use Illuminate\Http\Request;
use Modules\Listings\Models\Listing;
use App\Models\User;
use App\Models\Industry;
use Modules\ListingGallery\Models\ListingGalleryImage;
use Modules\ListingLocations\Models\ListingLocation;
use Modules\ListingFaqs\Models\ListingFaq;
use Modules\ListingSeo\Models\ListingSeoSetting;
use Modules\ListingBranding\Models\ListingBrandingSetting;
use Modules\ListingHero\Models\ListingHero;
use Modules\ListingAbout\Models\ListingAbout;
use Modules\ListingServices\Models\ListingService;
use Modules\ListingProducts\Models\ListingProduct;
use Modules\ListingReviews\Models\ListingReview;
use Modules\ListingLeads\Models\ListingLead;
use Modules\ListingAppointments\Models\ListingAppointment;
use Modules\ListingAppointments\Models\ListingAppointmentSlot;
use Modules\Properties\Models\Property;
use Modules\ListingClients\Models\ListingClient;
use Modules\ListingRestaurantMenu\Entities\MenuProduct;
use Modules\ListingRestaurantMenu\Entities\MenuCategory;
use Modules\ListingOfficeHours\Models\ListingSchedule;
use Modules\ListingTeamMembers\Models\ListingTeamMember;
use Modules\ListingTeamMembers\Models\TeamMemberPosition;
use Modules\ListingPackages\Models\ListingPackage;
use Modules\VCards\Models\VCard;
use Modules\ClientFidelity\Models\ClientFidelityCard;
use Modules\ClientFidelity\Models\FidelityReward;

class ApiExplorerController extends Controller
{
    public function index(Request $request)
    {
        $fetchData = $request->session()->get('fetchData');
        $fetchError = $request->session()->get('fetchError');
        $request->session()->forget(['fetchData', 'fetchError']);

        $endpoints = [
            'businesses' => [
                'title' => 'Businesses',
                'description' => 'Lista de negocios',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings', 'description' => 'Lista paginada de negocios'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}', 'description' => 'Detalle de negocio'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/stats', 'description' => 'Estadisticas del negocio'],
                ],
            ],
            'business_data' => [
                'title' => 'Business Data',
                'description' => 'Datos de modulos de negocio',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/locations', 'description' => 'Ubicaciones'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/gallery', 'description' => 'Galeria de imagenes'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/faqs', 'description' => 'Preguntas frecuentes'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/seo', 'description' => 'Configuracion SEO'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/branding', 'description' => 'Colores y marca'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/hero', 'description' => 'Seccion hero'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/about', 'description' => 'Seccion about'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/services', 'description' => 'Servicios'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/products', 'description' => 'Productos'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/reviews', 'description' => 'Reseñas'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/leads', 'description' => 'Leads'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/appointments', 'description' => 'Citas'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/appointment-slots', 'description' => 'Horarios de citas'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/properties', 'description' => 'Propiedades'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/clients', 'description' => 'Clientes'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/menu-categories', 'description' => 'Categorias menu'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/menu-products', 'description' => 'Productos menu'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/office-hours', 'description' => 'Horarios de oficina'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/team-members', 'description' => 'Miembros del equipo'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/team-member-positions', 'description' => 'Puestos del equipo'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/packages', 'description' => 'Paquetes'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/vcards', 'description' => 'vCards'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/fidelity-cards', 'description' => 'Tarjetas de fidelidad'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/listings/{id}/fidelity-rewards', 'description' => 'Recompensas de fidelidad'],
                ],
            ],
            'industries' => [
                'title' => 'Industries',
                'description' => 'Industrias del sistema',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/api/v1/admin/industries', 'description' => 'Lista de industrias'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/industries/{id}', 'description' => 'Detalle de industria'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/industries/{id}/modules', 'description' => 'Modulos de la industria'],
                ],
            ],
            'users' => [
                'title' => 'Users',
                'description' => 'Usuarios del sistema',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/api/v1/admin/users', 'description' => 'Lista paginada de usuarios'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/users/{id}', 'description' => 'Detalle de usuario'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/users/{id}/businesses', 'description' => 'Negocios del usuario'],
                ],
            ],
        ];

        $businesses = Listing::select('id', 'name', 'slug')
            ->orderBy('name')
            ->limit(50)
            ->get();

        return inertia('Admin/ApiExplorer/Index', [
            'endpoints' => $endpoints,
            'businesses' => $businesses,
            'baseUrl' => config('app.url'),
            'fetchData' => $fetchData,
            'fetchError' => $fetchError,
        ]);
    }

    public function fetch(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'listing_id' => 'nullable|integer|exists:listings,id',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $path = $request->get('path');
        $businessId = $request->get('listing_id');
        $userId = $request->get('user_id');

        $path = preg_replace('/\{id\}/', $businessId, $path);
        $path = preg_replace('/\{business\}/', $businessId, $path);
        $path = preg_replace('/\{user\}/', $userId, $path);

        try {
            $result = $this->executeEndpoint($path, $businessId, $userId);
            return back()->with('fetchData', ['status' => 200, 'body' => $result]);
        } catch (\Exception $e) {
            return back()->with('fetchError', $e->getMessage());
        }
    }

    private function executeEndpoint(string $path, ?int $businessId, ?int $userId): array
    {
        $perPage = min((int) request()->get('per_page', 20), 100);

        if ($path === '/api/v1/admin/listings') {
            $businesses = Listing::with(['user:id,name,email', 'user.subscriptions.plan:id,name', 'modules.moduleDefinition'])
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);
            return [
                'data' => BusinessListResource::collection($businesses->items()),
                'meta' => [
                    'current_page' => $businesses->currentPage(),
                    'per_page' => $businesses->perPage(),
                    'total' => $businesses->total(),
                    'last_page' => $businesses->lastPage(),
                ],
            ];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId) {
            $business = Listing::with(['user:id,name,email', 'user.subscriptions.plan:id,name,limits', 'modules.moduleDefinition'])->findOrFail($businessId);
            return ['data' => new BusinessResource($business)];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/stats') {
            $business = Listing::findOrFail($businessId);
            return ['data' => [
                'locations' => $business->locations()->count(),
                'gallery' => $business->galleryImages()->count(),
                'faqs' => $business->faqs()->count(),
                'services' => $business->services()->count(),
                'products' => $business->products()->count(),
                'reviews' => $business->reviews()->count(),
                'leads' => $business->leads()->count(),
                'properties' => $business->properties()->count(),
                'clients' => $business->clients()->count(),
            ]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/locations') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'locations')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $locations = ListingLocation::where('listing_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $locations->isEmpty() ? ['data' => null, 'message' => 'No hay ubicaciones configuradas'] : ['data' => $locations, 'meta' => ['total' => $locations->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/gallery') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'gallery')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $images = ListingGalleryImage::where('listing_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $images->isEmpty() ? ['data' => null, 'message' => 'No hay imagenes en la galeria'] : ['data' => $images, 'meta' => ['total' => $images->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/faqs') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'faqs')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $faqs = ListingFaq::where('listing_id', $business->id)->with('category:id,name')->orderBy('sort_order', 'asc')->get();
            return $faqs->isEmpty() ? ['data' => null, 'message' => 'No hay preguntas frecuentes'] : ['data' => $faqs, 'meta' => ['total' => $faqs->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/services') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'services')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $services = ListingService::where('listing_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $services->isEmpty() ? ['data' => null, 'message' => 'No hay servicios configurados'] : ['data' => $services, 'meta' => ['total' => $services->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/seo') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'seo')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $seo = ListingSeoSetting::where('listing_id', $business->id)->first();
            return !$seo ? ['data' => null, 'message' => 'No hay configuracion SEO'] : ['data' => $seo];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/branding') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'branding')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $branding = ListingBrandingSetting::where('listing_id', $business->id)->first();
            return !$branding ? ['data' => null, 'message' => 'No hay configuracion de marca'] : ['data' => $branding];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/hero') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'hero')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $hero = ListingHero::where('listing_id', $business->id)->first();
            return !$hero ? ['data' => null, 'message' => 'No hay configuracion de hero'] : ['data' => $hero];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/about') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'about')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $about = ListingAbout::where('listing_id', $business->id)->first();
            return !$about ? ['data' => null, 'message' => 'No hay seccion about'] : ['data' => $about];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/products') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'products')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $products = ListingProduct::where('listing_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $products->isEmpty() ? ['data' => null, 'message' => 'No hay productos configurados'] : ['data' => $products, 'meta' => ['total' => $products->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/reviews') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'reviews')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $reviews = ListingReview::where('listing_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $reviews->isEmpty() ? ['data' => null, 'message' => 'No hay reviews'] : ['data' => $reviews, 'meta' => ['total' => $reviews->count(), 'average_rating' => $reviews->avg('rating')]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/leads') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'leads')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $leads = ListingLead::where('listing_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $leads->isEmpty() ? ['data' => null, 'message' => 'No hay leads'] : ['data' => $leads, 'meta' => ['total' => $leads->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/appointments') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'appointments')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $appointments = ListingAppointment::where('listing_id', $business->id)
                ->with(['location:id,name', 'service:id,name'])
                ->orderBy('appointment_date', 'desc')
                ->paginate($perPage);
            return $appointments->isEmpty() ? ['data' => null, 'message' => 'No hay citas'] : ['data' => $appointments->items(), 'meta' => ['current_page' => $appointments->currentPage(), 'per_page' => $appointments->perPage(), 'total' => $appointments->total(), 'last_page' => $appointments->lastPage()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/appointment-slots') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'appointments')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $slots = ListingAppointmentSlot::where('listing_id', $business->id)->with(['service:id,name', 'location:id,name'])->orderBy('day_of_week')->orderBy('start_time')->get();
            return $slots->isEmpty() ? ['data' => null, 'message' => 'No hay horarios configurados'] : ['data' => $slots, 'meta' => ['total' => $slots->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/properties') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'properties')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $properties = Property::where('listing_id', $business->id)
                ->with(['propertyType:id,name,key', 'images'])
                ->orderBy('created_at', 'desc')
                ->get();
            return $properties->isEmpty() ? ['data' => null, 'message' => 'No hay propiedades'] : ['data' => $properties, 'meta' => ['total' => $properties->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/clients') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'clients')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $clients = ListingClient::where('listing_id', $business->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return $clients->isEmpty() ? ['data' => null, 'message' => 'No hay clientes'] : ['data' => $clients, 'meta' => ['total' => $clients->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/menu-categories') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'restaurant_menu')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $categories = MenuCategory::where('listing_id', $business->id)
                ->with(['parent:id,title', 'children:id,parent_id,title'])
                ->orderBy('sort_order')
                ->get();
            return $categories->isEmpty() ? ['data' => null, 'message' => 'No hay categorias'] : ['data' => $categories, 'meta' => ['total' => $categories->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/menu-products') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'restaurant_menu')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $products = MenuProduct::where('listing_id', $business->id)
                ->with(['category:id,title', 'variants', 'images'])
                ->orderBy('sort_order')
                ->get();
            return $products->isEmpty() ? ['data' => null, 'message' => 'No hay productos'] : ['data' => $products, 'meta' => ['total' => $products->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/office-hours') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'office_hours')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $schedules = ListingSchedule::where('listing_id', $business->id)
                ->with(['location:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();
            return $schedules->isEmpty() ? ['data' => null, 'message' => 'No hay horarios'] : ['data' => $schedules, 'meta' => ['total' => $schedules->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/team-members') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'team_members')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $members = ListingTeamMember::where('listing_id', $business->id)
                ->with(['position:id,name'])
                ->orderBy('sort_order')
                ->get();
            return $members->isEmpty() ? ['data' => null, 'message' => 'No hay miembros'] : ['data' => $members, 'meta' => ['total' => $members->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/team-positions') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'team_members')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $positions = TeamMemberPosition::where('listing_id', $business->id)
                ->orderBy('sort_order')
                ->get();
            return $positions->isEmpty() ? ['data' => null, 'message' => 'No hay puestos'] : ['data' => $positions, 'meta' => ['total' => $positions->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/team-member-positions') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'team_members')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $positions = TeamMemberPosition::where('listing_id', $business->id)
                ->orderBy('sort_order')
                ->get();
            return $positions->isEmpty() ? ['data' => null, 'message' => 'No hay puestos'] : ['data' => $positions, 'meta' => ['total' => $positions->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/packages') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'packages')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $packages = ListingPackage::where('listing_id', $business->id)
                ->orderBy('sort_order')
                ->get();
            return $packages->isEmpty() ? ['data' => null, 'message' => 'No hay paquetes'] : ['data' => $packages, 'meta' => ['total' => $packages->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/vcards') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'vcards')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $vcards = VCard::where('listing_id', $business->id)
                ->with(['team:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();
            return $vcards->isEmpty() ? ['data' => null, 'message' => 'No hay vCards'] : ['data' => $vcards, 'meta' => ['total' => $vcards->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/fidelity-cards') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'client_fidelity')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $cards = ClientFidelityCard::where('listing_id', $business->id)
                ->with(['reward:id,name,description'])
                ->orderBy('created_at', 'desc')
                ->get();
            return $cards->isEmpty() ? ['data' => null, 'message' => 'No hay tarjetas'] : ['data' => $cards, 'meta' => ['total' => $cards->count()]];
        }

        if ($path === '/api/v1/admin/listings/' . $businessId . '/fidelity-rewards') {
            $business = Listing::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'client_fidelity')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $rewards = FidelityReward::where('listing_id', $business->id)
                ->orderBy('sort_order')
                ->get();
            return $rewards->isEmpty() ? ['data' => null, 'message' => 'No hay recompensas'] : ['data' => $rewards, 'meta' => ['total' => $rewards->count()]];
        }

        if ($path === '/api/v1/admin/users') {
            $users = User::with(['subscriptions.plan:id,name'])->orderBy('created_at', 'desc')->paginate($perPage);
            return [
                'data' => UserListResource::collection($users->items()),
                'meta' => [
                    'current_page' => $users->currentPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'last_page' => $users->lastPage(),
                ],
            ];
        }

        if ($path === '/api/v1/admin/users/' . $userId) {
            $user = User::with(['subscriptions.plan:id,name,limits'])->findOrFail($userId);
            return ['data' => new UserResource($user)];
        }

        if ($path === '/api/v1/admin/users/' . $userId . '/businesses') {
            $user = User::findOrFail($userId);
            $businesses = Listing::where('user_id', $user->id)->with(['subscriptions.plan:id,name'])->orderBy('created_at', 'desc')->get(['id', 'name', 'slug', 'is_active', 'created_at']);
            return ['data' => $businesses, 'meta' => ['total' => $businesses->count()]];
        }

        if ($path === '/api/v1/admin/industries') {
            $industries = Industry::with('moduleDefinitions')
                ->orderBy('name')
                ->get();
            return [
                'data' => IndustryResource::collection($industries),
                'meta' => ['total' => $industries->count()],
            ];
        }

        if ($path === '/api/v1/admin/industries/' . $businessId) {
            $industry = Industry::with('moduleDefinitions')->findOrFail($businessId);
            return ['data' => new IndustryResource($industry)];
        }

        if ($path === '/api/v1/admin/industries/' . $businessId . '/modules') {
            $industry = Industry::findOrFail($businessId);
            $modules = $industry->moduleDefinitions;
            return [
                'data' => $modules->map(function ($module) {
                    return [
                        'id' => $module->id,
                        'module_key' => $module->key,
                        'module_name' => $module->name,
                        'icon' => $module->icon,
                        'is_premium' => (bool) $module->is_premium,
                    ];
                }),
                'meta' => ['total' => $modules->count()],
            ];
        }

        throw new \Exception('Endpoint not found: ' . $path);
    }
}
