<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\ListingAppointments\Models\ListingAppointment;
use Modules\Listings\Models\Listing;
use Modules\ListingGallery\Models\ListingGallery;
use Modules\ListingLeads\Models\ListingLead;
use Modules\ListingLocations\Models\ListingLocation;
use Modules\ListingPackages\Models\ListingPackage;
use Modules\ListingServices\Models\ListingService;

class BusinessController extends Controller
{
    protected $themeSections = [
        ['key' => 'hero', 'module' => null, 'label' => 'Hero Principal'],
        ['key' => 'about', 'module' => null, 'label' => 'Acerca de'],
        ['key' => 'services', 'module' => 'services', 'label' => 'Servicios'],
        ['key' => 'gallery', 'module' => 'gallery', 'label' => 'Galería'],
        ['key' => 'products', 'module' => 'products', 'label' => 'Productos'],
        ['key' => 'menu', 'module' => 'restaurant_menu', 'label' => 'Menú'],
        ['key' => 'appointments', 'module' => 'appointments', 'label' => 'Turnos'],
        ['key' => 'contact', 'module' => 'contact_form', 'label' => 'Contacto'],
        ['key' => 'reviews', 'module' => 'reviews', 'label' => 'Reseñas'],
        ['key' => 'locations', 'module' => 'locations', 'label' => 'Ubicaciones'],
        ['key' => 'promotions', 'module' => 'promotions', 'label' => 'Promociones'],
        ['key' => 'packages', 'module' => 'packages', 'label' => 'Paquetes'],
    ];

    public function show(string $slug)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->getEnabledModules();

        $theme = $business->minisiteTheme;

        $brandingSetting = $business->brandingSetting;
        $brandingCss = $brandingSetting?->generated_css;
        $sectionVariants = $brandingSetting?->section_variants ?? ['services' => 'cards'];

        $branding = [
            'generated_css' => $brandingCss,
            'page_style' => $brandingSetting?->page_style,
            'section_style' => $brandingSetting?->section_style,
            'hero_style' => $brandingSetting?->hero_style,
            'buttons_uppercase' => $brandingSetting?->buttons_uppercase,
            'dark_mode' => $brandingSetting?->dark_mode,
        ];

        $services = [];
        if (in_array('services', $modules)) {
            $services = $business->services()
                ->where('is_active', true)
                ->where('allows_online_booking', true)
                ->orderBy('sort_order')
                ->get(['id', 'name', 'description', 'price', 'duration_minutes']);
        }

        $locations = [];
        if (in_array('locations', $modules)) {
            $locations = $business->locations()
                ->where('is_active', true)
                ->orderBy('is_primary', 'desc')
                ->get(['id', 'name', 'address_line_1', 'city']);
        }

        $gallery = [];
        if (in_array('gallery', $modules)) {
            $primary = ListingGallery::primaryFor($business->id);

            $gallery = $primary
                ? $business->galleryImages()
                    ->where('is_active', true)
                    ->where('business_gallery_id', $primary->id)
                    ->orderBy('sort_order')
                    ->limit(12)
                    ->get()
                : collect();
        }

        $reviews = [];
        if (in_array('reviews', $modules)) {
            $reviews = $business->reviews()
                ->where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();
        }

        $promotions = [];
        if (in_array('promotions', $modules)) {
            $promotions = $business->promotions()
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('starts_at')
                        ->orWhere('starts_at', '<=', now());
                })
                ->where(function ($query) {
                    $query->whereNull('expires_at')
                        ->orWhere('expires_at', '>=', now());
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        $products = [];
        if (in_array('products', $modules)) {
            $products = $business->products()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->limit(12)
                ->get(['id', 'name', 'description', 'price']);
        }

        $packages = [];
        if (in_array('packages', $modules)) {
            $packages = $business->packages()
                ->where('is_active', true)
                ->with('features')
                ->orderBy('sort_order')
                ->limit(12)
                ->get(['id', 'title', 'short_description', 'image', 'price', 'promo_price', 'whatsapp', 'whatsapp_message']);
        }

        $menuCategories = [];
        $menuProducts = [];
        if (in_array('restaurant_menu', $modules)) {
            $menuCategories = \Modules\ListingRestaurantMenu\Entities\MenuCategory::where('listing_id', $business->id)
                ->whereNull('parent_id')
                ->where('active', true)
                ->with(['images', 'children.images', 'children' => function ($q) {
                    $q->where('active', true)->orderBy('sort_order');
                }, 'products' => function ($q) {
                    $q->where('active', true)->orderBy('sort_order');
                }])
                ->orderBy('sort_order')
                ->get()
                ->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'title' => $cat->title,
                        'image' => $cat->images->first()?->path,
                        'products' => $cat->products->map(function ($p) {
                            return [
                                'id' => $p->id,
                                'title' => $p->title,
                                'image' => $p->image,
                                'display_price' => $p->display_price,
                            ];
                        }),
                        'children' => $cat->children->map(function ($child) {
                            return [
                                'id' => $child->id,
                                'title' => $child->title,
                                'image' => $child->images->first()?->path,
                                'products' => $child->products->map(function ($p) {
                                    return [
                                        'id' => $p->id,
                                        'title' => $p->title,
                                        'image' => $p->image,
                                        'display_price' => $p->display_price,
                                    ];
                                }),
                            ];
                        }),
                    ];
                });
        }

        $socialNetworks = [];
        if (in_array('socialmedia', $modules)) {
            $socialNetworks = $business->socialNetworks()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->get(['id', 'platform', 'url', 'username', 'show_on_hero', 'show_on_footer', 'show_on_contact']);
        }

        $hero = $business->hero;

        $about = null;
        if (in_array('about', $modules)) {
            $about = $business->about;
        }

        return Inertia::render('Public/Business/Show', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'description' => $business->description,
                'phone' => $business->phone,
                'email' => $business->email,
                'website' => $business->website,
                'timezone' => $business->timezone,
                'currency' => $business->currency,
                'listing_type' => $business->listing_type->value ?? $business->listing_type,
                'logo_path' => $business->logo_path,
                'cover_image_path' => $business->cover_image_path,
            ],
            'theme' => $theme ? [
                'id' => $theme->id,
                'name' => $theme->name,
                'slug' => $theme->slug,
                'css_variables' => $theme->css_variables,
                'layout_config' => $theme->layout_config,
                'section_config' => $theme->section_config,
                'scheme_palettes' => $theme->section_config['scheme_palettes'] ?? [],
            ] : null,
            'theme_css_variables' => $theme?->css_variables ? json_encode($theme->css_variables) : null,
            'sectionSchemes' => $theme?->section_config['section_schemes'] ?? [
                'hero' => 'gradient',
                'about' => 'light',
                'services' => 'neutral',
                'products' => 'light',
                'gallery' => 'dark',
                'menu' => 'neutral',
                'appointments' => 'primary',
                'reviews' => 'light',
                'locations' => 'neutral',
                'contact' => 'dark',
                'promotions' => 'accent',
            ],
            'schemePalettes' => $theme?->section_config['scheme_palettes'] ?? [],
            'header_colors' => $theme?->section_config['header_colors'] ?? [
                'bg' => 'white',
                'text' => 'brand_text',
                'heading' => 'brand_primary',
                'link' => 'brand_primary',
                'link_hover' => 'brand_accent',
                'border' => 'rgba(0,0,0,0.1)',
                'nav_bg' => 'white',
                'nav_text' => 'brand_text',
                'nav_link' => 'brand_primary',
                'nav_link_hover' => 'brand_accent',
                'nav_border' => 'rgba(0,0,0,0.1)',
            ],
            'footer_colors' => $theme?->section_config['footer_colors'] ?? [
                'bg' => 'brand_text',
                'text' => 'white',
                'heading' => 'white',
                'link' => 'white',
                'link_hover' => 'rgba(255,255,255,0.8)',
                'border' => 'rgba(255,255,255,0.1)',
                'nav_bg' => 'brand_text',
                'nav_text' => 'white',
                'nav_link' => 'white',
                'nav_link_hover' => 'rgba(255,255,255,0.8)',
                'nav_border' => 'rgba(255,255,255,0.1)',
            ],
            'themeSections' => $this->themeSections,
            'modules' => $modules,
            'services' => $services,
            'locations' => $locations,
            'gallery' => $gallery,
            'reviews' => $reviews,
            'promotions' => $promotions,
            'products' => $products,
            'packages' => $packages,
            'menuCategories' => $menuCategories,
            'menuProducts' => $menuProducts,
            'socialNetworks' => $socialNetworks,
            'hero' => $hero ? [
                'id' => $hero->id,
                'title' => $hero->title,
                'subtitle' => $hero->subtitle,
                'text_aux' => $hero->text_aux,
                'background_type' => $hero->background_type,
                'background_color' => $hero->background_color,
                'background_gradient_start' => $hero->background_gradient_start,
                'background_gradient_end' => $hero->background_gradient_end,
                'background_image_path' => $hero->background_image_path,
                'alignment' => $hero->alignment,
                'buttons' => $hero->buttons,
                'show_contact_info' => $hero->show_contact_info,
                'show_social_links' => $hero->show_social_links,
            ] : null,
            'about' => $about ? [
                'id' => $about->id,
                'title' => $about->title,
                'subtitle' => $about->subtitle,
                'description' => $about->description,
                'image_path' => $about->image_path,
                'logo_path' => $about->logo_path,
            ] : null,
            'branding' => $branding,
            'sectionVariants' => $sectionVariants,
        ]);
    }

    public function services(string $slug)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('services', $modules)) {
            abort(404);
        }

        $services = $business->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->with('location')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'description', 'price', 'duration_minutes', 'whatsapp_contact', 'image']);

        $theme = $business->minisiteTheme;
        $brandingSetting = $business->brandingSetting;
        $socialNetworks = in_array('socialmedia', $modules)
            ? $business->socialNetworks()->where('is_active', true)->orderBy('sort_order')->get(['id', 'platform', 'url', 'username', 'show_on_hero', 'show_on_footer', 'show_on_contact'])
            : [];
        $locations = $business->locations()->where('is_active', true)->orderBy('is_primary', 'desc')->get(['id', 'name', 'address_line_1', 'city']);

        return Inertia::render('Public/Business/Services', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'phone' => $business->phone,
                'email' => $business->email,
                'logo_path' => $business->logo_path,
                'website' => $business->website,
            ],
            'services' => $services,
            'theme' => $theme ? ['id' => $theme->id, 'name' => $theme->name, 'slug' => $theme->slug, 'css_variables' => $theme->css_variables, 'layout_config' => $theme->layout_config, 'section_config' => $theme->section_config] : null,
            'theme_css_variables' => $theme?->css_variables ? json_encode($theme->css_variables) : null,
            'modules' => $modules,
            'branding' => ['generated_css' => $brandingSetting?->generated_css, 'page_style' => $brandingSetting?->page_style, 'section_style' => $brandingSetting?->section_style, 'hero_style' => $brandingSetting?->hero_style, 'buttons_uppercase' => $brandingSetting?->buttons_uppercase, 'dark_mode' => $brandingSetting?->dark_mode],
            'socialNetworks' => $socialNetworks,
            'locations' => $locations,
        ]);
    }

    public function gallery(string $slug)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('gallery', $modules)) {
            abort(404);
        }

        $images = $business->galleryImages()
            ->where('is_active', true)
            ->with('location')
            ->orderBy('sort_order')
            ->get();

        $theme = $business->minisiteTheme;
        $brandingSetting = $business->brandingSetting;
        $socialNetworks = in_array('socialmedia', $modules)
            ? $business->socialNetworks()->where('is_active', true)->orderBy('sort_order')->get(['id', 'platform', 'url', 'username', 'show_on_hero', 'show_on_footer', 'show_on_contact'])
            : [];
        $locations = $business->locations()->where('is_active', true)->orderBy('is_primary', 'desc')->get(['id', 'name', 'address_line_1', 'city']);

        return Inertia::render('Public/Business/Gallery', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'phone' => $business->phone,
                'email' => $business->email,
                'logo_path' => $business->logo_path,
                'website' => $business->website,
            ],
            'images' => $images,
            'theme' => $theme ? ['id' => $theme->id, 'name' => $theme->name, 'slug' => $theme->slug, 'css_variables' => $theme->css_variables, 'layout_config' => $theme->layout_config, 'section_config' => $theme->section_config] : null,
            'theme_css_variables' => $theme?->css_variables ? json_encode($theme->css_variables) : null,
            'modules' => $modules,
            'branding' => ['generated_css' => $brandingSetting?->generated_css, 'page_style' => $brandingSetting?->page_style, 'section_style' => $brandingSetting?->section_style, 'hero_style' => $brandingSetting?->hero_style, 'buttons_uppercase' => $brandingSetting?->buttons_uppercase, 'dark_mode' => $brandingSetting?->dark_mode],
            'socialNetworks' => $socialNetworks,
            'locations' => $locations,
        ]);
    }

    public function products(string $slug)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('products', $modules)) {
            abort(404);
        }

        $products = $business->products()
            ->where('is_active', true)
            ->with('location')
            ->orderBy('sort_order')
            ->get(['id', 'name', 'description', 'price', 'sku', 'quantity', 'whatsapp_contact']);

        $theme = $business->minisiteTheme;
        $brandingSetting = $business->brandingSetting;
        $socialNetworks = in_array('socialmedia', $modules)
            ? $business->socialNetworks()->where('is_active', true)->orderBy('sort_order')->get(['id', 'platform', 'url', 'username', 'show_on_hero', 'show_on_footer', 'show_on_contact'])
            : [];
        $locations = $business->locations()->where('is_active', true)->orderBy('is_primary', 'desc')->get(['id', 'name', 'address_line_1', 'city']);

        return Inertia::render('Public/Business/Products', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'phone' => $business->phone,
                'email' => $business->email,
                'logo_path' => $business->logo_path,
                'website' => $business->website,
            ],
            'products' => $products,
            'theme' => $theme ? ['id' => $theme->id, 'name' => $theme->name, 'slug' => $theme->slug, 'css_variables' => $theme->css_variables, 'layout_config' => $theme->layout_config, 'section_config' => $theme->section_config] : null,
            'theme_css_variables' => $theme?->css_variables ? json_encode($theme->css_variables) : null,
            'modules' => $modules,
            'branding' => ['generated_css' => $brandingSetting?->generated_css, 'page_style' => $brandingSetting?->page_style, 'section_style' => $brandingSetting?->section_style, 'hero_style' => $brandingSetting?->hero_style, 'buttons_uppercase' => $brandingSetting?->buttons_uppercase, 'dark_mode' => $brandingSetting?->dark_mode],
            'socialNetworks' => $socialNetworks,
            'locations' => $locations,
        ]);
    }

    public function book(string $slug, Request $request, AvailabilityService $availability)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('appointments', $modules)) {
            abort(404);
        }

        $serviceId = $request->input('service');
        $locationId = $request->input('location');
        $date = $request->input('date', now()->toDateString());

        $services = $business->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price']);

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1', 'city']);

        $selectedService = $serviceId
            ? $services->firstWhere('id', (int) $serviceId)
            : $services->first();

        $availableSlots = [];
        if ($selectedService) {
            $availableSlots = $availability->getAvailableSlotsForDate(
                $business,
                $date,
                $selectedService->duration_minutes
            );
        }

        return Inertia::render('Public/Business/Book', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'phone' => $business->phone,
                'email' => $business->email,
                'logo_path' => $business->logo_path,
                'website' => $business->website,
                'timezone' => $business->timezone,
            ],
            'services' => $services,
            'locations' => $locations,
            'selectedService' => $selectedService,
            'availableSlots' => $availableSlots,
            'selectedDate' => $date,
            'selectedLocation' => $locationId ? $locations->firstWhere('id', (int) $locationId) : null,
            'theme' => $business->minisiteTheme ? ['id' => $business->minisiteTheme->id, 'name' => $business->minisiteTheme->name, 'slug' => $business->minisiteTheme->slug, 'css_variables' => $business->minisiteTheme->css_variables, 'layout_config' => $business->minisiteTheme->layout_config, 'section_config' => $business->minisiteTheme->section_config] : null,
            'theme_css_variables' => $business->minisiteTheme?->css_variables ? json_encode($business->minisiteTheme->css_variables) : null,
            'modules' => $modules,
            'branding' => ['generated_css' => $business->brandingSetting?->generated_css, 'page_style' => $business->brandingSetting?->page_style, 'section_style' => $business->brandingSetting?->section_style, 'hero_style' => $business->brandingSetting?->hero_style, 'buttons_uppercase' => $business->brandingSetting?->buttons_uppercase, 'dark_mode' => $business->brandingSetting?->dark_mode],
            'socialNetworks' => [],
            'allLocations' => $locations,
        ]);
    }

    public function storeBooking(string $slug, Request $request, AvailabilityService $availability)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('appointments', $modules)) {
            abort(404);
        }

        $data = $request->validate([
            'service_id' => ['required', 'exists:listing_services,id'],
            'location_id' => ['required', 'exists:listing_locations,id'],
            'appointment_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'customer_name' => ['required', 'string', 'max:150'],
            'customer_email' => ['required', 'email', 'max:150'],
            'customer_phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $service = ListingService::findOrFail($data['service_id']);
        $location = ListingLocation::findOrFail($data['location_id']);

        if (! $service->allows_online_booking) {
            return back()->withErrors(['start_time' => 'Este servicio no permite reservas en línea.']);
        }

        $slotCheck = $availability->isSlotAvailable(
            $business,
            $data['appointment_date'],
            $data['start_time'],
            null,
            $service->duration_minutes
        );

        if (! $slotCheck['available']) {
            return back()->withErrors(['start_time' => $slotCheck['reason']]);
        }

        $endTime = date('H:i', strtotime($data['start_time'].' + '.$service->duration_minutes.' minutes'));

        $appointment = ListingAppointment::create([
            'listing_id' => $business->id,
            'business_location_id' => $location->id,
            'business_service_id' => $service->id,
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'end_time' => $endTime,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()->route('public.business.booking.success', $slug)
            ->with('appointment_id', $appointment->id);
    }

    public function bookingSuccess(string $slug, Request $request)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        $theme = $business->minisiteTheme;
        $brandingSetting = $business->brandingSetting;
        $socialNetworks = in_array('socialmedia', $modules)
            ? $business->socialNetworks()->where('is_active', true)->orderBy('sort_order')->get(['id', 'platform', 'url', 'username', 'show_on_hero', 'show_on_footer', 'show_on_contact'])
            : [];
        $locations = $business->locations()->where('is_active', true)->orderBy('is_primary', 'desc')->get(['id', 'name', 'address_line_1', 'city']);

        return Inertia::render('Public/Business/BookingSuccess', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'phone' => $business->phone,
                'email' => $business->email,
                'logo_path' => $business->logo_path,
                'website' => $business->website,
            ],
            'theme' => $theme ? ['id' => $theme->id, 'name' => $theme->name, 'slug' => $theme->slug, 'css_variables' => $theme->css_variables, 'layout_config' => $theme->layout_config, 'section_config' => $theme->section_config] : null,
            'theme_css_variables' => $theme?->css_variables ? json_encode($theme->css_variables) : null,
            'modules' => $modules,
            'branding' => ['generated_css' => $brandingSetting?->generated_css, 'page_style' => $brandingSetting?->page_style, 'section_style' => $brandingSetting?->section_style, 'hero_style' => $brandingSetting?->hero_style, 'buttons_uppercase' => $brandingSetting?->buttons_uppercase, 'dark_mode' => $brandingSetting?->dark_mode],
            'socialNetworks' => $socialNetworks,
            'locations' => $locations,
        ]);
    }

    public function contact(string $slug, Request $request)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('contact_form', $modules)) {
            abort(404);
        }

        return Inertia::render('Public/Business/Contact', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'email' => $business->email,
                'phone' => $business->phone,
                'logo_path' => $business->logo_path,
                'website' => $business->website,
            ],
            'theme' => $business->minisiteTheme ? ['id' => $business->minisiteTheme->id, 'name' => $business->minisiteTheme->name, 'slug' => $business->minisiteTheme->slug, 'css_variables' => $business->minisiteTheme->css_variables, 'layout_config' => $business->minisiteTheme->layout_config, 'section_config' => $business->minisiteTheme->section_config] : null,
            'theme_css_variables' => $business->minisiteTheme?->css_variables ? json_encode($business->minisiteTheme->css_variables) : null,
            'modules' => $modules,
            'branding' => ['generated_css' => $business->brandingSetting?->generated_css, 'page_style' => $business->brandingSetting?->page_style, 'section_style' => $business->brandingSetting?->section_style, 'hero_style' => $business->brandingSetting?->hero_style, 'buttons_uppercase' => $business->brandingSetting?->buttons_uppercase, 'dark_mode' => $business->brandingSetting?->dark_mode],
            'socialNetworks' => [],
            'locations' => [],
        ]);
    }

    public function storeContact(string $slug, Request $request)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('contact_form', $modules)) {
            abort(404);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $lead = ListingLead::create([
            'listing_id' => $business->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => $data['message'],
            'source' => 'website',
            'status' => 'new',
        ]);

        return redirect()->back()->with('success', 'Mensaje enviado correctamente.');
    }

    public function formByShortcode(string $slug, string $shortcode, Request $request)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('contact_form', $modules)) {
            abort(404);
        }

        $form = $business->contactForms()->where('shortcode', $shortcode)->first();

        if (! $form) {
            abort(404);
        }

        $fields = $form->activeFields->map(fn ($field) => [
            'id' => $field->id,
            'name' => $field->field_name,
            'type' => $field->field_type,
            'label' => $field->label,
            'placeholder' => $field->placeholder,
            'is_required' => $field->is_required,
            'options' => $field->options ?? [],
        ]);

        $theme = $business->minisiteTheme;
        $brandingSetting = $business->brandingSetting;
        $socialNetworks = in_array('socialmedia', $modules)
            ? $business->socialNetworks()->where('is_active', true)->orderBy('sort_order')->get(['id', 'platform', 'url', 'username', 'show_on_hero', 'show_on_footer', 'show_on_contact'])
            : [];
        $locations = $business->locations()->where('is_active', true)->orderBy('is_primary', 'desc')->get(['id', 'name', 'address_line_1', 'city']);

        return Inertia::render('Public/Business/FormByShortcode', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'email' => $business->email,
                'phone' => $business->phone,
                'logo_path' => $business->logo_path,
                'website' => $business->website,
            ],
            'form' => [
                'id' => $form->id,
                'name' => $form->name,
                'description' => $form->description,
                'shortcode' => $form->shortcode,
                'success_message' => $form->success_message,
                'show_phone' => $form->show_phone,
                'show_email' => $form->show_email,
            ],
            'fields' => $fields,
            'theme' => $theme ? ['id' => $theme->id, 'name' => $theme->name, 'slug' => $theme->slug, 'css_variables' => $theme->css_variables, 'layout_config' => $theme->layout_config, 'section_config' => $theme->section_config] : null,
            'theme_css_variables' => $theme?->css_variables ? json_encode($theme->css_variables) : null,
            'modules' => $modules,
            'branding' => ['generated_css' => $brandingSetting?->generated_css, 'page_style' => $brandingSetting?->page_style, 'section_style' => $brandingSetting?->section_style, 'hero_style' => $brandingSetting?->hero_style, 'buttons_uppercase' => $brandingSetting?->buttons_uppercase, 'dark_mode' => $brandingSetting?->dark_mode],
            'socialNetworks' => $socialNetworks,
            'locations' => $locations,
        ]);
    }

    public function storeFormByShortcode(string $slug, string $shortcode, Request $request)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('contact_form', $modules)) {
            abort(404);
        }

        $form = $business->contactForms()->where('shortcode', $shortcode)->first();

        if (! $form) {
            abort(404);
        }

        $rules = [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:50'],
        ];

        $fields = $form->activeFields;
        foreach ($fields as $field) {
            $fieldName = $field->field_name;
            $fieldRules = [];

            if ($field->is_required && ! in_array($field->field_type, ['checkbox'])) {
                $fieldRules[] = 'required';
            } else {
                $fieldRules[] = 'nullable';
            }

            if ($field->field_type === 'email' && $fieldName !== 'email') {
                $fieldRules[] = 'email';
            }

            $rules[$fieldName] = $fieldRules;
        }

        $data = $request->validate($rules);

        $notes = [];
        $metadata = [];

        foreach ($form->activeFields as $field) {
            if (in_array($field->field_name, ['name', 'email', 'phone'])) {
                continue;
            }

            $value = $data[$field->field_name] ?? null;

            if ($field->field_type === 'checkbox') {
                $value = $request->has($field->field_name) ? 'Si' : 'No';
            }

            if ($value !== null) {
                if ($field->field_type === 'select' && is_array($value)) {
                    $value = implode(', ', $value);
                }
                $metadata[$field->field_name] = $value;
            }
        }

        $lead = ListingLead::create([
            'listing_id' => $business->id,
            'business_contact_form_id' => $form->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => null,
            'metadata' => $metadata,
            'source' => 'form:'.$form->shortcode,
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', $form->success_message ?? 'Mensaje enviado correctamente.');
    }

    public function locations(string $slug)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('locations', $modules)) {
            abort(404);
        }

        $locations = $business->locations()
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->get();

        $theme = $business->minisiteTheme;
        $brandingSetting = $business->brandingSetting;
        $socialNetworks = in_array('socialmedia', $modules)
            ? $business->socialNetworks()->where('is_active', true)->orderBy('sort_order')->get(['id', 'platform', 'url', 'username', 'show_on_hero', 'show_on_footer', 'show_on_contact'])
            : [];

        return Inertia::render('Public/Business/Locations', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'phone' => $business->phone,
                'email' => $business->email,
                'logo_path' => $business->logo_path,
                'website' => $business->website,
            ],
            'locations' => $locations,
            'theme' => $theme ? ['id' => $theme->id, 'name' => $theme->name, 'slug' => $theme->slug, 'css_variables' => $theme->css_variables, 'layout_config' => $theme->layout_config, 'section_config' => $theme->section_config] : null,
            'theme_css_variables' => $theme?->css_variables ? json_encode($theme->css_variables) : null,
            'modules' => $modules,
            'branding' => ['generated_css' => $brandingSetting?->generated_css, 'page_style' => $brandingSetting?->page_style, 'section_style' => $brandingSetting?->section_style, 'hero_style' => $brandingSetting?->hero_style, 'buttons_uppercase' => $brandingSetting?->buttons_uppercase, 'dark_mode' => $brandingSetting?->dark_mode],
            'socialNetworks' => $socialNetworks,
        ]);
    }

    public function packages(string $slug)
    {
        $business = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->where('is_published', true)
            ->firstOrFail();

        $modules = $business->modules()->where('is_enabled', true)->get()->pluck('moduleDefinition.key')->toArray();
        if (! in_array('packages', $modules)) {
            abort(404);
        }

        $packages = ListingPackage::where('listing_id', $business->id)
            ->where('is_active', true)
            ->with('features')
            ->orderBy('sort_order')
            ->get();

        $defaultWhatsapp = $business->phone;
        $defaultMessage = 'Hola, me interesa el paquete';

        return response()->json([
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo_path' => $business->logo_path,
                'phone' => $business->phone,
            ],
            'packages' => $packages->map(function ($package) use ($defaultWhatsapp, $defaultMessage) {
                return [
                    'id' => $package->id,
                    'title' => $package->title,
                    'short_description' => $package->short_description,
                    'long_description' => $package->long_description,
                    'image' => $package->image,
                    'price' => $package->price,
                    'promo_price' => $package->promo_price,
                    'whatsapp' => $package->whatsapp ?? $defaultWhatsapp,
                    'whatsapp_message' => $package->whatsapp_message ?? str_replace('{package_title}', $package->title, $defaultMessage),
                    'features' => $package->features->pluck('name')->toArray(),
                ];
            }),
        ]);
    }
}
