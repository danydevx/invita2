<?php

namespace Modules\ListingMinisite\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Services\Properties\PropertyFormSchemaService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingMinisite\Models\ListingMinisiteSection;
use Modules\ListingPackages\Models\ListingPackage;
use Modules\ListingMinisite\Models\ListingMinisiteSetting;
use Modules\Properties\Models\PropertyValue;

class ListingMinisiteController extends Controller
{
    public function show(string $slug)
    {
        $listing = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$listing) {
            abort(404);
        }

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            abort(404, 'Minisite no configurado');
        }

        $sections = ListingMinisiteSection::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($section) use ($listing) {
                $config = $section->config ?? [];

                $sectionData = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'section_key' => $section->section_key,
                    'title' => $section->title,
                    'subtitle' => $section->subtitle,
                    'description' => $section->description,
                    'buttons' => $section->buttons ?? [],
                    'config' => $config,
                ];

                switch ($section->section_type) {
                    case 'services':
                        $sectionData['items'] = $this->getServicesData($listing, $config);
                        break;
                    case 'gallery':
                        $sectionData['items'] = $this->getGalleryData($listing, $config);
                        break;
                    case 'promotions':
                        $sectionData['items'] = $this->getPromotionsData($listing, $config);
                        break;
                    case 'contact_form':
                        $sectionData['form'] = $this->getContactFormData($listing, $config);
                        break;
                    case 'appointments':
                        $sectionData['appointments'] = $this->getAppointmentsData($listing, $config);
                        break;
                    case 'availability':
                        $sectionData['availability'] = $this->getAvailabilityData($listing, $config);
                        break;
                    case 'locations':
                        $sectionData['items'] = $this->getLocationsData($listing, $config);
                        break;
                    case 'about':
                        $sectionData['content'] = $this->getAboutData($listing, $config);
                        break;
                    case 'features':
                        $sectionData['items'] = $this->getFeaturesData($listing, $config);
                        break;
                    case 'faqs':
                        $sectionData['items'] = $this->getFaqsData($listing, $config);
                        break;
                    case 'products':
                        $sectionData['items'] = $this->getProductsData($listing, $config);
                        break;
                    case 'reviews':
                        $sectionData['items'] = $this->getReviewsData($listing, $config);
                        break;
                    case 'restaurant_menu':
                        $sectionData['items'] = $this->getRestaurantMenuData($listing, $config);
                        break;
                    case 'properties':
                        $sectionData['items'] = $this->getPropertiesData($listing, $config);
                        break;
                    case 'packages':
                        $sectionData['items'] = $this->getPackagesData($listing, $config);
                        break;
                }

                return $sectionData;
            });

        $socialNetworks = $listing->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($listing);

        $aiChatbot = $this->getAiChatbotSettings($listing);

        $orderSettings = null;
        if (class_exists('\Modules\Orders\Models\OrderSetting')) {
            $orderSettings = \Modules\Orders\Models\OrderSetting::where('listing_id', $listing->id)->first();
        }

        return Inertia::render($this->resolveThemeView('Show', $setting->theme_key), [
            'business' => [
                'id' => $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'logo' => $listing->logo,
                'cover_image' => $listing->cover_image_path,
                'whatsapp' => $listing->whatsapp,
            ],
            'setting' => [
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'hero_show_social' => $setting->hero_show_social,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ],
            'sections' => $sections,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
            'orderSettings' => $orderSettings,
        ]);
    }

    public function services(string $slug)
    {
        return $this->renderPage($slug, 'services', 'Servicios', fn($b) => ['items' => $this->getServicesData($b, [])]);
    }

    public function products(string $slug)
    {
        $listing = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$listing) {
            abort(404);
        }

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            abort(404, 'Minisite no configurado');
        }

        $categories = $listing->productCategories()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn($cat) => ['id' => $cat->id, 'name' => $cat->name])
            ->toArray();

        $products = $this->getProductsData($listing, []);

        $socialNetworks = $listing->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($listing);
        $aiChatbot = $this->getAiChatbotSettings($listing);

        $orderSettings = null;
        $businessLocations = [];
        if (class_exists('\Modules\Orders\Models\OrderSetting')) {
            $orderSettings = \Modules\Orders\Models\OrderSetting::where('listing_id', $listing->id)->first();
        }
        if (class_exists('\Modules\ListingLocations\Models\ListingLocation')) {
            $businessLocations = $listing->locations()
                ->where('is_active', true)
                ->get(['id', 'name', 'address_line_1', 'latitude', 'longitude'])
                ->map(function ($loc) {
                    return [
                        'id' => $loc->id,
                        'name' => $loc->name,
                        'address' => $loc->full_address ?? $loc->address_line_1,
                        'latitude' => $loc->latitude,
                        'longitude' => $loc->longitude,
                    ];
                })
                ->toArray();
        }

        return Inertia::render($this->resolveThemeView('Products', $setting->theme_key), [
            'business' => [
                'id' => $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'logo' => $listing->logo,
                'cover_image' => $listing->cover_image_path,
                'whatsapp' => $listing->whatsapp,
            ],
            'setting' => [
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'hero_show_social' => $setting->hero_show_social,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ],
            'pageTitle' => 'Productos',
            'sectionData' => [
                'items' => $products,
                'categories' => $categories,
            ],
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
            'orderSettings' => $orderSettings,
            'businessLocations' => $businessLocations,
        ]);
    }

    public function menu(string $slug)
    {
        $listing = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$listing) {
            abort(404);
        }

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            abort(404, 'Minisite no configurado');
        }

        $menuData = $this->getRestaurantMenuData($listing, []);

        $socialNetworks = $listing->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($listing);
        $aiChatbot = $this->getAiChatbotSettings($listing);

        $orderSettings = null;
        $businessLocations = [];
        if (class_exists('\Modules\Orders\Models\OrderSetting')) {
            $orderSettings = \Modules\Orders\Models\OrderSetting::where('listing_id', $listing->id)->first();
        }
        if (class_exists('\Modules\ListingLocations\Models\ListingLocation')) {
            $businessLocations = $listing->locations()
                ->where('is_active', true)
                ->get(['id', 'name', 'address_line_1', 'latitude', 'longitude'])
                ->map(function ($loc) {
                    return [
                        'id' => $loc->id,
                        'name' => $loc->name,
                        'address' => $loc->full_address ?? $loc->address_line_1,
                        'latitude' => $loc->latitude,
                        'longitude' => $loc->longitude,
                    ];
                })
                ->toArray();
        }

        return Inertia::render($this->resolveThemeView('Menu', $setting->theme_key), [
            'business' => [
                'id' => $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'logo' => $listing->logo,
                'cover_image' => $listing->cover_image_path,
                'whatsapp' => $listing->whatsapp,
            ],
            'setting' => [
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'hero_show_social' => $setting->hero_show_social,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ],
            'pageTitle' => 'Menú',
            'menuData' => $menuData,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
            'orderSettings' => $orderSettings,
            'businessLocations' => $businessLocations,
        ]);
    }

    public function productDetail(string $slug, string $productSlug)
    {
        $listing = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$listing) {
            abort(404);
        }

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            abort(404, 'Minisite no configurado');
        }

        $product = $listing->products()
            ->where('is_active', true)
            ->where('slug', $productSlug)
            ->with('images')
            ->first();

        if (!$product) {
            abort(404, 'Producto no encontrado');
        }

        $imagePath = $product->image;
        if (!$imagePath && $product->images && $product->images->isNotEmpty()) {
            $imagePath = $product->images->first()->path;
        }

        if ($imagePath) {
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                $finalPath = $imagePath;
            } else {
                $finalPath = "/storage/{$imagePath}";
            }
        } else {
            $finalPath = null;
        }

        $galleryImages = $product->images->map(function ($img) {
            $path = str_starts_with($img->path, 'http') ? $img->path : "/storage/{$img->path}";
            return [
                'id' => $img->id,
                'path' => $path,
                'title' => $img->title ?? '',
            ];
        })->toArray();

        $relatedProducts = $listing->products()
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->with('images')
            ->limit(4)
            ->get()
            ->map(function ($p) {
                $img = $p->image;
                if (!$img && $p->images->isNotEmpty()) {
                    $img = $p->images->first()->path;
                }
                $imgPath = $img ? "/storage/{$img}" : null;
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'price' => $p->price,
                    'image' => $imgPath,
                ];
            })->toArray();

        $socialNetworks = $listing->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($listing);
        $aiChatbot = $this->getAiChatbotSettings($listing);

        $orderSettings = null;
        if (class_exists('\Modules\Orders\Models\OrderSetting')) {
            $orderSettings = \Modules\Orders\Models\OrderSetting::where('listing_id', $listing->id)->first();
        }

        return Inertia::render($this->resolveThemeView('ProductDetail', $setting->theme_key), [
            'business' => [
                'id' => $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'logo' => $listing->logo,
                'cover_image' => $listing->cover_image_path,
                'whatsapp' => $listing->whatsapp,
            ],
            'setting' => [
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'hero_show_social' => $setting->hero_show_social,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ],
            'product' => [
                'id' => $product->id,
                'listing_id' => $listing->id,
                'name' => $product->name,
                'slug' => $product->slug,
                'description' => $product->description,
                'price' => $product->price,
                'compare_at_price' => $product->compare_at_price,
                'sku' => $product->sku,
                'barcode' => $product->barcode,
                'quantity' => $product->quantity,
                'whatsapp_contact' => $product->whatsapp_contact,
                'image' => $finalPath,
                'gallery' => $galleryImages,
            ],
            'relatedProducts' => $relatedProducts,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
            'orderSettings' => $orderSettings,
        ]);
    }

    public function serviceDetail(string $slug, string $serviceSlug)
    {
        $listing = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$listing) {
            abort(404);
        }

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            abort(404, 'Minisite no configurado');
        }

        $service = $listing->services()
            ->where('is_active', true)
            ->where('slug', $serviceSlug)
            ->with('images')
            ->first();

        if (!$service) {
            abort(404, 'Servicio no encontrado');
        }

        $imagePath = $service->image;
        if (!$imagePath && $service->images && $service->images->isNotEmpty()) {
            $imagePath = $service->images->first()->path;
        }

        if ($imagePath) {
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                $finalPath = $imagePath;
            } else {
                $finalPath = "/storage/{$imagePath}";
            }
        } else {
            $finalPath = null;
        }

        $galleryImages = $service->images->map(function ($img) {
            $path = str_starts_with($img->path, 'http') ? $img->path : "/storage/{$img->path}";
            return [
                'id' => $img->id,
                'path' => $path,
                'title' => $img->title ?? '',
            ];
        })->toArray();

        $socialNetworks = $listing->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($listing);

        return Inertia::render($this->resolveThemeView('ServiceDetail', $setting->theme_key), [
            'business' => [
                'id' => $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'logo' => $listing->logo,
                'cover_image' => $listing->cover_image_path,
            ],
            'setting' => [
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'hero_show_social' => $setting->hero_show_social,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ],
            'service' => [
                'id' => $service->id,
                'name' => $service->name,
                'slug' => $service->slug,
                'description' => $service->description,
                'price' => $service->price,
                'duration_minutes' => $service->duration_minutes,
                'deposit_amount' => $service->deposit_amount,
                'deposit_required' => $service->deposit_required,
                'allows_online_booking' => $service->allows_online_booking,
                'whatsapp_contact' => $service->whatsapp_contact,
                'image' => $finalPath,
                'gallery' => $galleryImages,
            ],
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
        ]);
    }

    public function gallery(string $slug)
    {
        return $this->renderPage($slug, 'gallery', 'Galería', fn($b) => ['items' => $this->getGalleryData($b, [])]);
    }

    public function appointments(string $slug)
    {
        return $this->renderPage($slug, 'appointments', 'Citas y Reservas', fn($b) => ['appointments' => $this->getAppointmentsData($b, [])]);
    }

    public function promotions(string $slug)
    {
        return $this->renderPage($slug, 'promotions', 'Promociones', fn($b) => ['items' => $this->getPromotionsData($b, [])]);
    }

    public function promotionDetail(string $slug, string $promotionSlug)
    {
        $listing = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$listing) {
            abort(404);
        }

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            abort(404, 'Minisite no configurado');
        }

        $promotion = $listing->promotions()
            ->where('is_active', true)
            ->where('slug', $promotionSlug)
            ->first();

        if (!$promotion) {
            abort(404, 'Promocion no encontrada');
        }

        $imagePath = $promotion->image;
        if ($imagePath) {
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                $finalPath = $imagePath;
            } else {
                $finalPath = "/storage/{$imagePath}";
            }
        } else {
            $finalPath = null;
        }

        $relatedPromotions = $listing->promotions()
            ->where('is_active', true)
            ->where('id', '!=', $promotion->id)
            ->limit(4)
            ->get()
            ->map(function ($p) {
                $imgPath = $p->image;
                if ($imgPath) {
                    if (str_starts_with($imgPath, 'http://') || str_starts_with($imgPath, 'https://')) {
                        $finalImgPath = $imgPath;
                    } else {
                        $finalImgPath = "/storage/{$imgPath}";
                    }
                } else {
                    $finalImgPath = null;
                }
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'slug' => $p->slug,
                    'promotion_price' => $p->promotion_price,
                    'regular_price' => $p->regular_price,
                    'image' => $finalImgPath,
                ];
            })->toArray();

        $socialNetworks = $listing->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($listing);

        $discountPercent = null;
        if ($promotion->regular_price && $promotion->promotion_price && $promotion->regular_price > $promotion->promotion_price) {
            $discountPercent = round((1 - $promotion->promotion_price / $promotion->regular_price) * 100);
        }

        return Inertia::render($this->resolveThemeView('PromotionDetail', $setting->theme_key), [
            'business' => [
                'id' => $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'logo' => $listing->logo,
                'cover_image' => $listing->cover_image_path,
            ],
            'setting' => [
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'hero_show_social' => $setting->hero_show_social,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ],
            'promotion' => [
                'id' => $promotion->id,
                'name' => $promotion->name,
                'slug' => $promotion->slug,
                'description' => $promotion->description,
                'regular_price' => $promotion->regular_price,
                'promotion_price' => $promotion->promotion_price,
                'discount_percent' => $discountPercent,
                'coupon_code' => $promotion->coupon_code,
                'qr_code_path' => $promotion->qr_code_path ? (
                    str_starts_with($promotion->qr_code_path, 'http://') || str_starts_with($promotion->qr_code_path, 'https://')
                        ? $promotion->qr_code_path
                        : "/storage/{$promotion->qr_code_path}"
                ) : null,
                'starts_at' => $promotion->starts_at,
                'expires_at' => $promotion->expires_at,
                'image' => $finalPath,
            ],
            'relatedPromotions' => $relatedPromotions,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
        ]);
    }

    public function locations(string $slug)
    {
        return $this->renderPage($slug, 'locations', 'Ubicaciones', fn($b) => ['items' => $this->getLocationsData($b, [])]);
    }

    public function reviews(string $slug)
    {
        return $this->renderPage($slug, 'reviews', 'Reseñas', fn($b) => ['items' => $this->getReviewsData($b, [])]);
    }

    public function faqs(string $slug)
    {
        return $this->renderPage($slug, 'faqs', 'Preguntas Frecuentes', fn($b) => ['items' => $this->getFaqsData($b, [])]);
    }

    public function contact(string $slug)
    {
        return $this->renderPage($slug, 'contact', 'Contacto', fn($b) => ['form' => $this->getContactFormData($b, [])]);
    }

    public function properties(string $slug)
    {
        return $this->renderPage($slug, 'properties', 'Propiedades', fn($b) => [
            'items' => $this->getPropertiesData($b, []),
            'property_types' => $this->getPropertyTypes($b),
        ]);
    }

    public function propertyDetail(string $slug, string $propertySlug)
    {
        $listing = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$listing) {
            abort(404);
        }

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            abort(404, 'Minisite no configurado');
        }

        $property = \Modules\Properties\Models\Property::where('listing_id', $listing->id)
            ->where('slug', $propertySlug)
            ->with(['images', 'propertyType', 'amenities.amenity', 'values.propertyField'])
            ->first();

        if (!$property) {
            abort(404, 'Propiedad no encontrada');
        }

        $imagePath = $property->main_image;
        if (!$imagePath && $property->images && $property->images->isNotEmpty()) {
            $firstImage = $property->images->first();
            $imagePath = $firstImage->path ?? null;
        }

        if ($imagePath) {
            if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                $finalPath = $imagePath;
            } else {
                $finalPath = "/storage/{$imagePath}";
            }
        } else {
            $finalPath = null;
        }

        $galleryImages = [];
        if ($finalPath) {
            $galleryImages[] = [
                'id' => 'main',
                'path' => $finalPath,
                'title' => $property->title,
            ];
        }
        foreach ($property->images as $img) {
            if (!empty($img->image_path)) {
                $path = str_starts_with($img->image_path, 'http') ? $img->image_path : "/storage/{$img->image_path}";
                if ($path !== $finalPath) {
                    $galleryImages[] = [
                        'id' => $img->id,
                        'path' => $path,
                        'title' => $img->alt_text ?? '',
                    ];
                }
            }
        }

        $amenities = $property->amenities->map(function ($pa) {
            return [
                'id' => $pa->amenity?->id,
                'name' => $pa->amenity?->name,
                'icon' => $pa->amenity?->icon ?? 'bi bi-check-circle',
            ];
        })->filter(fn($a) => $a['name'])->values()->toArray();

        $propertyData = [
            'id' => $property->id,
            'title' => $property->title,
            'slug' => $property->slug,
            'description' => $property->description,
            'operation_type' => $property->operation_type,
            'operation_label' => $property->getOperationLabel(),
            'price' => $property->price,
            'formatted_price' => $property->getFormattedPrice(),
            'currency' => $property->currency,
            'price_period' => $property->price_period,
            'main_image' => $finalPath,
            'gallery' => $galleryImages,
            'property_type' => $property->propertyType?->name,
            'property_type_key' => $property->propertyType?->key,
            'city' => $property->city,
            'state' => $property->state ?: $property->state_code,
            'country' => $property->country,
            'colony' => $property->colony,
            'municipality' => $property->municipality,
            'street' => $property->street,
            'exterior_number' => $property->exterior_number,
            'interior_number' => $property->interior_number,
            'postal_code' => $property->postal_code,
            'references' => $property->references,
            'full_address' => trim("{$property->street}, {$property->city}, {$property->state}"),
            'latitude' => $property->latitude,
            'longitude' => $property->longitude,
            'show_exact_location' => $property->show_exact_location,
            'amenities' => $amenities,
            'property_code' => $property->property_code,
            'status' => $property->status,
        ];

        $formSchema = [];
        if ($property->propertyType) {
            $formSchemaService = new PropertyFormSchemaService();
            $schema = $formSchemaService->getFormSchema($property->propertyType);

            $valuesByFieldKey = [];

            foreach ($property->values as $value) {
                $fieldKey = $value->propertyField?->field_key;
                if ($fieldKey) {
                    $valuesByFieldKey[$fieldKey] = $this->formatPropertyValue($value);
                }
            }

            $propertyFields = [
                'title' => $property->title,
                'description' => $property->description,
                'operation_type' => $property->operation_type,
                'price' => $property->price,
                'currency' => $property->currency,
                'price_period' => $property->price_period,
                'country' => $property->country,
                'state' => $property->state,
                'city' => $property->city,
                'municipality' => $property->municipality,
                'colony' => $property->colony,
                'street' => $property->street,
                'exterior_number' => $property->exterior_number,
                'interior_number' => $property->interior_number,
                'postal_code' => $property->postal_code,
                'references' => $property->references,
            ];

            foreach ($propertyFields as $key => $value) {
                if ($value !== null && $value !== '') {
                    $valuesByFieldKey[$key] = $value;
                }
            }

            $formSchema = array_map(function ($section) use ($valuesByFieldKey) {
                $section['fields'] = array_map(function ($field) use ($valuesByFieldKey) {
                    $field['value'] = $valuesByFieldKey[$field['field_key']] ?? null;
                    return $field;
                }, $section['fields']);
                return $section;
            }, $schema['sections']);
        }

        $socialNetworks = $listing->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $existingSections = $this->getExistingSections($listing);
        $aiChatbot = $this->getAiChatbotSettings($listing);

        return Inertia::render($this->resolveThemeView('PropertyDetail', $setting->theme_key), [
            'business' => [
                'id' => $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'logo' => $listing->logo,
                'cover_image' => $listing->cover_image_path,
                'whatsapp' => $listing->whatsapp,
                'phone' => $listing->phone,
                'email' => $listing->email,
            ],
            'setting' => [
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'hero_show_social' => $setting->hero_show_social,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ],
            'property' => $propertyData,
            'formSchema' => $formSchema,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
        ]);
    }

    protected function formatPropertyValue(PropertyValue $value): mixed
    {
        if ($value->value_text !== null) {
            return $value->value_text;
        }
        if ($value->value_number !== null) {
            return $value->value_number;
        }
        if ($value->value_boolean !== null) {
            return $value->value_boolean;
        }
        if ($value->value_date !== null) {
            return $value->value_date;
        }
        if ($value->value_json !== null) {
            return $value->value_json;
        }
        return null;
    }

    private function renderPage(string $slug, string $sectionType, string $pageTitle, callable $dataLoader): \Inertia\Response
    {
        $listing = Listing::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$listing) {
            abort(404);
        }

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->first();

        if (!$setting) {
            abort(404, 'Minisite no configurado');
        }

        $existingSections = $this->getExistingSections($listing);
        $pageData = $dataLoader($listing);
        $aiChatbot = $this->getAiChatbotSettings($listing);

        $socialNetworks = $listing->socialNetworks()
            ->where('is_active', true)
            ->get(['platform', 'url', 'icon_class']);

        $pageKeyMap = [
            'services' => 'Services',
            'products' => 'Products',
            'gallery' => 'Gallery',
            'appointments' => 'Appointments',
            'promotions' => 'Promotions',
            'locations' => 'Locations',
            'reviews' => 'Reviews',
            'faqs' => 'Faqs',
            'contact' => 'Contact',
            'properties' => 'Properties',
        ];

        return Inertia::render($this->resolveThemeView($pageKeyMap[$sectionType], $setting->theme_key), [
            'business' => [
                'id' => $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'logo' => $listing->logo,
                'cover_image' => $listing->cover_image_path,
            ],
            'setting' => [
                'theme_key' => $setting->theme_key,
                'hero_layout' => $setting->hero_layout,
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'hero_show_social' => $setting->hero_show_social,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ],
            'pageTitle' => $pageTitle,
            'sectionData' => $pageData,
            'socialNetworks' => $socialNetworks,
            'existingSections' => $existingSections,
            'aiChatbot' => $aiChatbot,
        ]);
    }

    private function getExistingSections(Listing $listing): array
    {
        $sections = [];

        if ($listing->services()->where('is_active', true)->exists()) {
            $sections[] = 'services';
        }
        if ($listing->products()->where('is_active', true)->exists()) {
            $sections[] = 'products';
        }
        if ($listing->galleryImages()->where('is_active', true)->exists()) {
            $sections[] = 'gallery';
        }
        if ($listing->appointments()->exists()) {
            $sections[] = 'appointments';
        }
        if ($listing->availability()->exists()) {
            $sections[] = 'availability';
        }
        if ($listing->promotions()->where('is_active', true)->exists()) {
            $sections[] = 'promotions';
        }
        if ($listing->locations()->where('is_active', true)->exists()) {
            $sections[] = 'locations';
        }
        if ($listing->reviews()->where('is_active', true)->exists()) {
            $sections[] = 'reviews';
        }
        if ($listing->faqs()->where('is_active', true)->exists()) {
            $sections[] = 'faqs';
        }
        if ($listing->contactForms()->where('is_active', true)->exists()) {
            $sections[] = 'contact_form';
        }
        if (\Modules\ListingRestaurantMenu\Entities\MenuCategory::where('listing_id', $listing->id)->where('active', true)->has('activeProducts')->exists()) {
            $sections[] = 'restaurant_menu';
        }
        if (\Modules\Properties\Models\Property::where('listing_id', $listing->id)->exists()) {
            $sections[] = 'properties';
        }

        return $sections;
    }

    private function getServicesData(Listing $listing, array $config): array
    {
        $query = $listing->services()
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['service_ids'])) {
            $query->whereIn('id', $config['service_ids']);
        }

        if (empty($config['service_ids'])) {
            $limit = 20;
            $query->limit($limit);
        }

        return $query
            ->with('images')
            ->get(['id', 'name', 'slug', 'description', 'price', 'duration_minutes', 'deposit_amount', 'deposit_required', 'allows_online_booking', 'whatsapp_contact', 'image'])
            ->map(function ($service) {
                $imagePath = $service->image;
                if (!$imagePath && $service->images && $service->images->isNotEmpty()) {
                    $imagePath = $service->images->first()->path;
                }

                if ($imagePath) {
                    if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                        $finalPath = $imagePath;
                    } else {
                        $finalPath = "/storage/{$imagePath}";
                    }
                } else {
                    $finalPath = null;
                }

                $galleryImages = $service->images->map(function ($img) {
                    $path = str_starts_with($img->path, 'http') ? $img->path : "/storage/{$img->path}";
                    return [
                        'id' => $img->id,
                        'path' => $path,
                        'title' => $img->title ?? '',
                    ];
                })->toArray();

                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'slug' => $service->slug,
                    'description' => $service->description,
                    'price' => $service->price,
                    'duration_minutes' => $service->duration_minutes,
                    'deposit_amount' => $service->deposit_amount,
                    'deposit_required' => $service->deposit_required,
                    'allows_online_booking' => $service->allows_online_booking,
                    'whatsapp_contact' => $service->whatsapp_contact,
                    'image' => $finalPath,
                    'gallery' => $galleryImages,
                ];
            })->toArray();
    }

    private function getGalleryData(Listing $listing, array $config): array
    {
        $galleryId = $config['gallery_id'] ?? null;
        $limit = $config['images_limit'] ?? 10;

        $query = $listing->galleryImages()
            ->where('is_active', true);

        if ($galleryId) {
            $query->where('business_gallery_id', $galleryId);
        }

        return $query
            ->orderBy('sort_order')
            ->limit($limit)
            ->get(['id', 'path', 'title', 'description'])
            ->map(function ($image) {
                $path = $image->path;
                if ($path && !str_starts_with($path, 'http')) {
                    $path = '/storage/' . $path;
                }
                return [
                    'id' => $image->id,
                    'path' => $path,
                    'title' => $image->title,
                    'description' => $image->description,
                ];
            })->toArray();
    }

    private function getPromotionsData(Listing $listing, array $config): array
    {
        $query = $listing->promotions()
            ->where('is_active', true);

        if (!empty($config['promotion_ids'])) {
            $query->whereIn('id', $config['promotion_ids']);
        }

        return $query
            ->orderBy('sort_order')
            ->get(['id', 'name', 'slug', 'description', 'image', 'promotion_price', 'expires_at', 'regular_price', 'coupon_code'])
            ->map(function ($promo) {
                return [
                    'id' => $promo->id,
                    'slug' => $promo->slug,
                    'name' => $promo->name,
                    'description' => $promo->description,
                    'regular_price' => $promo->regular_price,
                    'promotion_price' => $promo->promotion_price,
                    'expires_at' => $promo->expires_at,
                    'coupon_code' => $promo->coupon_code,
                    'image' => $promo->image,
                ];
            })->toArray();
    }

    private function getContactFormData(Listing $listing, array $config): ?array
    {
        $formId = $config['form_id'] ?? null;

        if (!$formId) {
            return null;
        }

        $form = $listing->contactForms()
            ->where('is_active', true)
            ->find($formId);

        if (!$form) {
            return null;
        }

        $form->load('fields');

        return [
            'id' => $form->id,
            'shortcode' => $form->shortcode,
            'fields' => $form->fields->map(fn($f) => $f->getConfig())->toArray(),
        ];
    }

    private function getAppointmentsData(Listing $listing, array $config): array
    {
        $services = $listing->services()
            ->where('is_active', true)
            ->where('allows_online_booking', true)
            ->orderBy('name')
            ->get(['id', 'name', 'duration_minutes', 'price'])
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'duration_minutes' => $service->duration_minutes,
                    'price' => $service->price,
                ];
            })->toArray();

        $locations = $listing->locations()
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1', 'city'])
            ->map(function ($location) {
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address_line_1,
                    'city' => $location->city,
                ];
            })->toArray();

        $availableDays = $listing->availability()
            ->where('is_available', true)
            ->pluck('day_of_week')
            ->toArray();

        return [
            'services' => $services,
            'locations' => $locations,
            'availableDays' => $availableDays,
        ];
    }

    private function getAvailabilityData(Listing $listing, array $config): array
    {
        $schedule = $listing->availability()
            ->orderBy('day_of_week')
            ->get(['day_of_week', 'is_available', 'start_time', 'end_time', 'slot_duration_minutes'])
            ->map(function ($day) {
                return [
                    'day_of_week' => $day->day_of_week,
                    'day_name' => \Modules\ListingAppointments\Models\ListingAvailability::dayShortName($day->day_of_week),
                    'is_available' => $day->is_available,
                    'start_time' => $day->start_time,
                    'end_time' => $day->end_time,
                    'slot_duration_minutes' => $day->slot_duration_minutes,
                ];
            })
            ->toArray();

        $exceptions = $listing->availabilityExceptions()
            ->orderBy('exception_date')
            ->get(['exception_date', 'is_available', 'start_time', 'end_time', 'reason'])
            ->map(function ($exc) {
                return [
                    'exception_date' => $exc->exception_date->format('Y-m-d'),
                    'is_available' => $exc->is_available,
                    'start_time' => $exc->start_time,
                    'end_time' => $exc->end_time,
                    'reason' => $exc->reason,
                ];
            })
            ->toArray();

        return [
            'schedule' => $schedule,
            'exceptions' => $exceptions,
        ];
    }

    private function getRestaurantMenuData(Listing $listing, array $config): array
    {
        $query = \Modules\ListingRestaurantMenu\Entities\MenuCategory::where('listing_id', $listing->id)
            ->where('active', true)
            ->whereNull('parent_id')
            ->with(['children' => function ($q) {
                $q->where('active', true)->orderBy('sort_order');
            }, 'activeProducts', 'children.activeProducts']);

        if (!empty($config['category_ids'])) {
            $query->whereIn('id', $config['category_ids']);
        }

        $categories = $query->orderBy('sort_order')->get();

        return $categories->map(function ($category) use ($config) {
            $products = $category->activeProducts->map(function ($product) use ($config) {
                return [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->display_price,
                    'base_price' => $product->base_price,
                    'has_variants' => $product->activeVariants->count() > 0,
                    'variants' => $product->activeVariants->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'title' => $variant->title,
                            'description' => $variant->description,
                            'price' => $variant->display_price,
                        ];
                    })->toArray(),
                    'image' => $product->image,
                    'gallery' => $product->images->map(fn($img) => ['id' => $img->id, 'path' => $img->path, 'title' => $img->title])->toArray(),
                ];
            })->toArray();

            $children = $category->children->map(function ($child) use ($config) {
                $childProducts = $child->activeProducts->map(function ($product) use ($config) {
                    return [
                        'id' => $product->id,
                        'title' => $product->title,
                        'slug' => $product->slug,
                        'description' => $product->description,
                        'price' => $product->display_price,
                        'base_price' => $product->base_price,
                        'has_variants' => $product->activeVariants->count() > 0,
                        'variants' => $product->activeVariants->map(function ($variant) {
                            return [
                                'id' => $variant->id,
                                'title' => $variant->title,
                                'description' => $variant->description,
                                'price' => $variant->display_price,
                            ];
                        })->toArray(),
                        'image' => $product->image,
                        'gallery' => $product->images->map(fn($img) => ['id' => $img->id, 'path' => $img->path, 'title' => $img->title])->toArray(),
                    ];
                })->toArray();

                return [
                    'id' => $child->id,
                    'title' => $child->title,
                    'products' => $childProducts,
                ];
            })->toArray();

            return [
                'id' => $category->id,
                'title' => $category->title,
                'description' => $category->description,
                'products' => $products,
                'children' => $children,
            ];
        })->toArray();
    }

    private function getLocationsData(Listing $listing, array $config): array
    {
        $query = $listing->locations()
            ->where('is_active', true);

        if (!empty($config['location_ids'])) {
            $query->whereIn('id', $config['location_ids']);
        }

        return $query
            ->orderByDesc('is_primary')
            ->orderBy('name')
            ->get(['id', 'name', 'address_line_1', 'city', 'state', 'state_code', 'country', 'phone', 'email', 'latitude', 'longitude', 'directions_url'])
            ->map(function ($location) {
                $statePart = $location->state ?: $location->state_code;
                $schedules = $location->schedules()
                    ->where('is_active', true)
                    ->get(['id', 'name', 'days_of_week', 'opening_time', 'closing_time', 'lunch_start_time', 'lunch_end_time'])
                    ->map(function ($schedule) {
                        return [
                            'id' => $schedule->id,
                            'name' => $schedule->name,
                            'days_display' => $schedule->days_display,
                            'time_display' => $schedule->time_display,
                        ];
                    })->toArray();
                return [
                    'id' => $location->id,
                    'name' => $location->name,
                    'address' => $location->address_line_1,
                    'city' => $location->city,
                    'state' => $statePart,
                    'country' => $location->country,
                    'full_address' => trim("{$location->address_line_1}, {$location->city}, {$statePart}"),
                    'phone' => $location->phone,
                    'email' => $location->email,
                    'latitude' => $location->latitude,
                    'longitude' => $location->longitude,
                    'directions_url' => $location->directions_url,
                    'schedules' => $schedules,
                ];
            })->toArray();
    }

    private function getAboutData(Listing $listing, array $config): array
    {
        return [
            'name' => $listing->name,
            'description' => $listing->description ?? '',
            'logo' => $listing->logo,
            'image' => $listing->image,
        ];
    }

    private function getFeaturesData(Listing $listing, array $config): array
    {
        $query = $listing->features()
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['feature_ids'])) {
            $query->whereIn('id', $config['feature_ids']);
        }

        return $query
            ->get(['id', 'title', 'description', 'icon'])
            ->map(function ($feature) {
                return [
                    'id' => $feature->id,
                    'title' => $feature->title,
                    'description' => $feature->description,
                    'icon' => $feature->icon ?? 'bi bi-check-circle',
                ];
            })->toArray();
    }

    private function getFaqsData(Listing $listing, array $config): array
    {
        $query = $listing->faqs()
            ->where('is_active', true);

        if (!empty($config['faq_ids'])) {
            $query->whereIn('id', $config['faq_ids']);
        }

        if (!empty($config['category_id'])) {
            $query->where('category_id', $config['category_id']);
        }

        return $query
            ->orderBy('sort_order')
            ->get(['id', 'question', 'answer', 'category_id'])
            ->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category_id' => $faq->category_id,
                ];
            })->toArray();
    }

    private function getProductsData(Listing $listing, array $config): array
    {
        $query = $listing->products()
            ->with('images', 'category', 'location')
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['product_ids'])) {
            $query->whereIn('id', $config['product_ids']);
        }

        return $query
            ->get(['id', 'name', 'slug', 'description', 'price', 'compare_at_price', 'sku', 'barcode', 'quantity', 'whatsapp_contact', 'image', 'category_id', 'business_location_id'])
            ->map(function ($product) {
                $imagePath = $product->image;
                if (!$imagePath && $product->images && $product->images->isNotEmpty()) {
                    $imagePath = $product->images->first()->path;
                }

                if ($imagePath) {
                    if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                        $finalPath = $imagePath;
                    } else {
                        $finalPath = "/storage/{$imagePath}";
                    }
                } else {
                    $finalPath = null;
                }

                $galleryImages = $product->images->map(function ($img) {
                    $path = str_starts_with($img->path, 'http') ? $img->path : "/storage/{$img->path}";
                    return [
                        'id' => $img->id,
                        'path' => $path,
                        'title' => $img->title ?? '',
                    ];
                })->toArray();

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => $product->price,
                    'compare_at_price' => $product->compare_at_price,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'quantity' => $product->quantity,
                    'whatsapp_contact' => $product->whatsapp_contact,
                    'image' => $finalPath,
                    'gallery' => $galleryImages,
                    'category_id' => $product->category_id,
                    'category_name' => $product->category?->name,
                    'location_id' => $product->business_location_id,
                    'location_name' => $product->location?->name,
                ];
            })->toArray();
    }

    private function getReviewsData(Listing $listing, array $config): array
    {
        $query = $listing->reviews()
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['review_ids'])) {
            $query->whereIn('id', $config['review_ids']);
        }

        $maxItems = $config['max_items'] ?? 10;
        $query->limit($maxItems);

        return $query
            ->get(['id', 'client_name', 'company', 'comment', 'rating', 'google_link'])
            ->map(function ($review) {
                return [
                    'id' => $review->id,
                    'client_name' => $review->client_name,
                    'company' => $review->company,
                    'comment' => $review->comment,
                    'rating' => $review->rating,
                    'google_link' => $review->google_link,
                ];
            })->toArray();
    }

    private function getPropertiesData(Listing $listing, array $config): array
    {
        $query = \Modules\Properties\Models\Property::where('listing_id', $listing->id)
            ->orderBy('is_featured', 'desc')
            ->orderBy('created_at', 'desc');

        if (!empty($config['property_ids'])) {
            $query->whereIn('id', $config['property_ids']);
        }

        $maxItems = $config['max_items'] ?? 12;
        $query->limit($maxItems);

        return $query
            ->with('images', 'propertyType')
            ->get()
            ->map(function ($property) {
                $imagePath = $property->main_image;
                if (!$imagePath && $property->images && $property->images->isNotEmpty()) {
                    $firstImage = $property->images->first();
                    $imagePath = $firstImage->path ?? null;
                }

                if ($imagePath) {
                    if (str_starts_with($imagePath, 'http://') || str_starts_with($imagePath, 'https://')) {
                        $finalPath = $imagePath;
                    } else {
                        $finalPath = "/storage/{$imagePath}";
                    }
                } else {
                    $finalPath = null;
                }

                $galleryImages = $property->images->map(function ($img) {
                    $path = str_starts_with($img->path, 'http') ? $img->path : "/storage/{$img->path}";
                    return [
                        'id' => $img->id,
                        'path' => $path,
                        'title' => $img->title ?? '',
                    ];
                })->toArray();

                return [
                    'id' => $property->id,
                    'title' => $property->title,
                    'slug' => $property->slug,
                    'description' => $property->description,
                    'operation_type' => $property->operation_type,
                    'operation_label' => $property->getOperationLabel(),
                    'price' => $property->price,
                    'formatted_price' => $property->getFormattedPrice(),
                    'currency' => $property->currency,
                    'price_period' => $property->price_period,
                    'main_image' => $finalPath,
                    'gallery' => $galleryImages,
                    'property_type' => $property->propertyType?->name,
                    'property_type_key' => $property->propertyType?->key,
                    'city' => $property->city,
                    'state' => $property->state ?: $property->state_code,
                    'country' => $property->country,
                    'full_address' => trim("{$property->street}, {$property->city}, {$property->state}"),
                    'latitude' => $property->latitude,
                    'longitude' => $property->longitude,
                ];
            })->toArray();
    }

    private function getPropertyTypes(Listing $listing): array
    {
        return \Modules\Properties\Models\PropertyType::where('listing_id', $listing->id)
            ->orWhereNull('listing_id')
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'key', 'name'])
            ->map(fn($type) => [
                'id' => $type->id,
                'key' => $type->key,
                'name' => $type->name,
            ])
            ->toArray();
    }

    private function resolveThemeView(string $page, ?string $theme = null): string
    {
        $theme = $theme ?: 'base';
        $overridePath = "Minisite.themes.{$theme}.{$page}";
        if (view()->exists($overridePath)) {
            return $overridePath;
        }
        return "Minisite.themes.base.{$page}";
    }

    private function getAiChatbotSettings($listing): ?array
    {
        $aiSetting = \Modules\ListingAiChatbot\Models\ListingAiSetting::where('listing_id', $listing->id)
            ->where('is_enabled', true)
            ->first();

        if (!$aiSetting) {
            return null;
        }

        return [
            'is_enabled' => true,
            'widget_color' => $aiSetting->widget_color,
            'widget_theme' => $aiSetting->widget_theme ?? 'light',
            'allow_reset_chat' => $aiSetting->allow_reset_chat ?? false,
        ];
    }

    private function getPackagesData(Listing $listing, array $config): array
    {
        $query = $listing->packages()
            ->where('is_active', true)
            ->with('features')
            ->orderBy('sort_order');

        if (!empty($config['package_ids'])) {
            $query->whereIn('id', $config['package_ids']);
        }

        return $query
            ->get()
            ->map(function ($package) {
                $image = $package->image;
                if ($image && !str_starts_with($image, 'http')) {
                    $image = '/storage/' . $image;
                }
                return [
                    'id' => $package->id,
                    'title' => $package->title,
                    'short_description' => $package->short_description,
                    'long_description' => $package->long_description,
                    'price' => $package->price,
                    'promo_price' => $package->promo_price,
                    'image' => $image,
                    'whatsapp' => $package->whatsapp,
                    'whatsapp_message' => $package->whatsapp_message,
                    'features' => $package->features->pluck('name')->toArray(),
                ];
            })->toArray();
    }
}