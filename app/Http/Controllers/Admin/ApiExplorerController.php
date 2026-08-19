<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BusinessListResource;
use App\Http\Resources\Api\V1\BusinessResource;
use App\Http\Resources\Api\V1\UserListResource;
use App\Http\Resources\Api\V1\UserResource;
use App\Http\Resources\Api\V1\IndustryResource;
use Illuminate\Http\Request;
use Modules\Businesses\Models\Business;
use App\Models\User;
use App\Models\Industry;
use Modules\Gallery\Models\BusinessGalleryImage;
use Modules\Locations\Models\BusinessLocation;
use Modules\Faqs\Models\BusinessFaq;
use Modules\Seo\Models\BusinessSeoSetting;
use Modules\Branding\Models\BusinessBrandingSetting;
use Modules\Hero\Models\BusinessHero;
use Modules\About\Models\BusinessAbout;
use Modules\Services\Models\BusinessService;
use Modules\Products\Models\BusinessProduct;
use Modules\Reviews\Models\BusinessReview;
use Modules\Leads\Models\BusinessLead;
use Modules\Appointments\Models\BusinessAppointment;
use Modules\Appointments\Models\BusinessAppointmentSlot;
use Modules\Properties\Models\Property;
use Modules\Clients\Models\BusinessClient;
use Modules\RestaurantMenu\Entities\MenuProduct;
use Modules\RestaurantMenu\Entities\MenuCategory;
use Modules\OfficeHours\Models\BusinessSchedule;
use Modules\TeamMembers\Models\BusinessTeamMember;
use Modules\TeamMembers\Models\TeamMemberPosition;
use Modules\Packages\Models\BusinessPackage;

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
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses', 'description' => 'Lista paginada de negocios'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}', 'description' => 'Detalle de negocio'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/stats', 'description' => 'Estadisticas del negocio'],
                ],
            ],
            'business_data' => [
                'title' => 'Business Data',
                'description' => 'Datos de modulos de negocio',
                'endpoints' => [
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/locations', 'description' => 'Ubicaciones'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/gallery', 'description' => 'Galeria de imagenes'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/faqs', 'description' => 'Preguntas frecuentes'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/seo', 'description' => 'Configuracion SEO'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/branding', 'description' => 'Colores y marca'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/hero', 'description' => 'Seccion hero'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/about', 'description' => 'Seccion about'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/services', 'description' => 'Servicios'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/products', 'description' => 'Productos'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/reviews', 'description' => 'Reseñas'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/leads', 'description' => 'Leads'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/appointments', 'description' => 'Citas'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/appointment-slots', 'description' => 'Horarios de citas'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/properties', 'description' => 'Propiedades'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/clients', 'description' => 'Clientes'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/menu-categories', 'description' => 'Categorias menu'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/menu-products', 'description' => 'Productos menu'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/office-hours', 'description' => 'Horarios de oficina'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/team-members', 'description' => 'Miembros del equipo'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/team-positions', 'description' => 'Puestos del equipo'],
                    ['method' => 'GET', 'path' => '/api/v1/admin/businesses/{id}/packages', 'description' => 'Paquetes'],
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

        $businesses = Business::select('id', 'name', 'slug')
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
            'business_id' => 'nullable|integer|exists:businesses,id',
            'user_id' => 'nullable|integer|exists:users,id',
        ]);

        $path = $request->get('path');
        $businessId = $request->get('business_id');
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

        if ($path === '/api/v1/admin/businesses') {
            $businesses = Business::with(['user:id,name,email', 'user.subscriptions.plan:id,name', 'modules.moduleDefinition', 'industry'])
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

        if ($path === '/api/v1/admin/businesses/' . $businessId) {
            $business = Business::with(['user:id,name,email', 'user.subscriptions.plan:id,name,limits', 'modules.moduleDefinition', 'industry'])->findOrFail($businessId);
            return ['data' => new BusinessResource($business)];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/stats') {
            $business = Business::findOrFail($businessId);
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

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/locations') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'locations')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $locations = BusinessLocation::where('business_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $locations->isEmpty() ? ['data' => null, 'message' => 'No hay ubicaciones configuradas'] : ['data' => $locations, 'meta' => ['total' => $locations->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/gallery') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'gallery')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $images = BusinessGalleryImage::where('business_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $images->isEmpty() ? ['data' => null, 'message' => 'No hay imagenes en la galeria'] : ['data' => $images, 'meta' => ['total' => $images->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/faqs') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'faqs')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $faqs = BusinessFaq::where('business_id', $business->id)->with('category:id,name')->orderBy('order', 'asc')->get();
            return $faqs->isEmpty() ? ['data' => null, 'message' => 'No hay preguntas frecuentes'] : ['data' => $faqs, 'meta' => ['total' => $faqs->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/services') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'services')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $services = BusinessService::where('business_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $services->isEmpty() ? ['data' => null, 'message' => 'No hay servicios configurados'] : ['data' => $services, 'meta' => ['total' => $services->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/seo') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'seo')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $seo = BusinessSeoSetting::where('business_id', $business->id)->first();
            return !$seo ? ['data' => null, 'message' => 'No hay configuracion SEO'] : ['data' => $seo];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/branding') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'branding')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $branding = BusinessBrandingSetting::where('business_id', $business->id)->first();
            return !$branding ? ['data' => null, 'message' => 'No hay configuracion de marca'] : ['data' => $branding];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/hero') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'hero')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $hero = BusinessHero::where('business_id', $business->id)->first();
            return !$hero ? ['data' => null, 'message' => 'No hay configuracion de hero'] : ['data' => $hero];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/about') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'about')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $about = BusinessAbout::where('business_id', $business->id)->first();
            return !$about ? ['data' => null, 'message' => 'No hay seccion about'] : ['data' => $about];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/products') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'products')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $products = BusinessProduct::where('business_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $products->isEmpty() ? ['data' => null, 'message' => 'No hay productos configurados'] : ['data' => $products, 'meta' => ['total' => $products->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/reviews') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'reviews')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $reviews = BusinessReview::where('business_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $reviews->isEmpty() ? ['data' => null, 'message' => 'No hay reviews'] : ['data' => $reviews, 'meta' => ['total' => $reviews->count(), 'average_rating' => $reviews->avg('rating')]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/leads') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'leads')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $leads = BusinessLead::where('business_id', $business->id)->orderBy('created_at', 'desc')->get();
            return $leads->isEmpty() ? ['data' => null, 'message' => 'No hay leads'] : ['data' => $leads, 'meta' => ['total' => $leads->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/appointments') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'appointments')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $appointments = BusinessAppointment::where('business_id', $business->id)
                ->with(['location:id,name', 'service:id,name'])
                ->orderBy('appointment_date', 'desc')
                ->paginate($perPage);
            return $appointments->isEmpty() ? ['data' => null, 'message' => 'No hay citas'] : ['data' => $appointments->items(), 'meta' => ['current_page' => $appointments->currentPage(), 'per_page' => $appointments->perPage(), 'total' => $appointments->total(), 'last_page' => $appointments->lastPage()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/appointment-slots') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'appointments')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $slots = BusinessAppointmentSlot::where('business_id', $business->id)->with(['service:id,name', 'location:id,name'])->orderBy('day_of_week')->orderBy('start_time')->get();
            return $slots->isEmpty() ? ['data' => null, 'message' => 'No hay horarios configurados'] : ['data' => $slots, 'meta' => ['total' => $slots->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/properties') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'properties')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $properties = Property::where('business_id', $business->id)
                ->with(['propertyType:id,name,key', 'images'])
                ->orderBy('created_at', 'desc')
                ->get();
            return $properties->isEmpty() ? ['data' => null, 'message' => 'No hay propiedades'] : ['data' => $properties, 'meta' => ['total' => $properties->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/clients') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'clients')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $clients = BusinessClient::where('business_id', $business->id)
                ->orderBy('created_at', 'desc')
                ->get();
            return $clients->isEmpty() ? ['data' => null, 'message' => 'No hay clientes'] : ['data' => $clients, 'meta' => ['total' => $clients->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/menu-categories') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'restaurant_menu')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $categories = MenuCategory::where('business_id', $business->id)
                ->with(['parent:id,title', 'children:id,parent_id,title'])
                ->orderBy('sort_order')
                ->get();
            return $categories->isEmpty() ? ['data' => null, 'message' => 'No hay categorias'] : ['data' => $categories, 'meta' => ['total' => $categories->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/menu-products') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'restaurant_menu')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $products = MenuProduct::where('business_id', $business->id)
                ->with(['category:id,title', 'variants', 'images'])
                ->orderBy('sort_order')
                ->get();
            return $products->isEmpty() ? ['data' => null, 'message' => 'No hay productos'] : ['data' => $products, 'meta' => ['total' => $products->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/office-hours') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'office_hours')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $schedules = BusinessSchedule::where('business_id', $business->id)
                ->with(['location:id,name'])
                ->orderBy('created_at', 'desc')
                ->get();
            return $schedules->isEmpty() ? ['data' => null, 'message' => 'No hay horarios'] : ['data' => $schedules, 'meta' => ['total' => $schedules->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/team-members') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'team_members')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $members = BusinessTeamMember::where('business_id', $business->id)
                ->with(['position:id,name'])
                ->orderBy('sort_order')
                ->get();
            return $members->isEmpty() ? ['data' => null, 'message' => 'No hay miembros'] : ['data' => $members, 'meta' => ['total' => $members->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/team-positions') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'team_members')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $positions = TeamMemberPosition::where('business_id', $business->id)
                ->orderBy('sort_order')
                ->get();
            return $positions->isEmpty() ? ['data' => null, 'message' => 'No hay puestos'] : ['data' => $positions, 'meta' => ['total' => $positions->count()]];
        }

        if ($path === '/api/v1/admin/businesses/' . $businessId . '/packages') {
            $business = Business::findOrFail($businessId);
            $module = $business->modules()->where('module_key', 'packages')->first();
            if (!$module || !$module->is_enabled) {
                return ['data' => null, 'message' => 'Modulo no habilitado en el plan'];
            }
            $packages = BusinessPackage::where('business_id', $business->id)
                ->orderBy('sort_order')
                ->get();
            return $packages->isEmpty() ? ['data' => null, 'message' => 'No hay paquetes'] : ['data' => $packages, 'meta' => ['total' => $packages->count()]];
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
            $businesses = Business::where('user_id', $user->id)->with(['subscriptions.plan:id,name'])->orderBy('created_at', 'desc')->get(['id', 'name', 'slug', 'is_active', 'created_at']);
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
