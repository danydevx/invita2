<?php

namespace Modules\Minisite\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Businesses\Models\Business;
use Modules\Gallery\Models\BusinessGallery;
use Modules\Minisite\Models\BusinessMinisiteSection;
use Modules\Minisite\Models\BusinessMinisiteSetting;
use Modules\ContactForm\Models\BusinessContactForm;

class MinisiteSectionController extends Controller
{
    public function index(Request $request, Business $business)
    {
        $this->authorize('manageSections', $business);

        $sections = BusinessMinisiteSection::where('business_id', $business->id)
            ->orderBy('sort_order')
            ->get()
            ->map(function ($section) use ($business) {
                $config = $section->config ?? [];
                $sectionData = [
                    'id' => $section->id,
                    'section_type' => $section->section_type,
                    'section_key' => $section->section_key,
                    'title' => $section->title,
                    'subtitle' => $section->subtitle,
                    'description' => $section->description,
                    'config' => $config,
                    'buttons' => $section->buttons ?? [],
                    'sort_order' => $section->sort_order,
                    'is_active' => $section->is_active,
                ];

                switch ($section->section_type) {
                    case 'services':
                        $sectionData['items'] = $this->getServicesData($business, $config);
                        break;
                    case 'gallery':
                        $sectionData['items'] = $this->getGalleryData($business, $config);
                        break;
                    case 'promotions':
                        $sectionData['items'] = $this->getPromotionsData($business, $config);
                        break;
                    case 'contact_form':
                        $sectionData['form'] = $this->getContactFormData($business, $config);
                        break;
                    case 'locations':
                        $sectionData['items'] = $this->getLocationsData($business, $config);
                        break;
                    case 'about':
                        $sectionData['content'] = $this->getAboutData($business, $config);
                        break;
                    case 'features':
                        $sectionData['items'] = $this->getFeaturesData($business, $config);
                        break;
                    case 'faqs':
                        $sectionData['items'] = $this->getFaqsData($business, $config);
                        break;
                    case 'products':
                        $sectionData['items'] = $this->getProductsData($business, $config);
                        break;
                    case 'packages':
                        $sectionData['items'] = $this->getPackagesData($business, $config);
                        break;
                }

                return $sectionData;
            });

        $setting = BusinessMinisiteSetting::where('business_id', $business->id)->first();

        if ($setting) {
            $heroSection = [
                'id' => 'hero',
                'section_type' => 'hero',
                'section_key' => 'hero',
                'title' => $setting->hero_title,
                'description' => null,
                'config' => [
                    'layout' => $setting->hero_layout ?? 'left',
                    'background_image' => $setting->hero_background_image,
                    'show' => true,
                ],
                'buttons' => [],
                'sort_order' => -2,
                'is_active' => true,
            ];

            $footerSection = [
                'id' => 'footer',
                'section_type' => 'footer',
                'section_key' => 'footer',
                'title' => null,
                'description' => null,
                'config' => [
                    'text' => $setting->footer_text,
                    'show_social' => $setting->footer_show_social,
                    'show' => true,
                ],
                'buttons' => [],
                'sort_order' => 9999,
                'is_active' => true,
            ];

            $sections = $sections->prepend($heroSection)->push($footerSection);
        }

        $socialNetworks = [];
        if (class_exists('\Modules\SocialMedia\Models\BusinessSocialNetwork')) {
            $socialNetworks = \Modules\SocialMedia\Models\BusinessSocialNetwork::where('business_id', $business->id)
                ->where('is_active', true)
                ->where('show_on_footer', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($sn) => [
                    'platform' => $sn->platform,
                    'url' => $sn->url,
                    'icon' => $sn->icon_class ?? 'bi bi-share',
                ])
                ->toArray();
        }

        return Inertia::render('Member/Minisite/Sections/Index', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
                'slug' => $business->slug,
                'logo' => $business->logo,
            ],
            'sections' => $sections,
            'setting' => $setting ? [
                'is_active' => $setting->is_active,
                'hero_layout' => $setting->hero_layout ?? 'left',
                'hero_title' => $setting->hero_title,
                'hero_subtitle' => $setting->hero_subtitle,
                'hero_background_image' => $setting->hero_background_image,
                'footer_text' => $setting->footer_text,
                'footer_show_social' => $setting->footer_show_social,
            ] : [
                'is_active' => false,
                'hero_layout' => 'left',
                'hero_title' => '',
                'hero_subtitle' => '',
                'hero_background_image' => null,
                'footer_text' => '',
                'footer_show_social' => true,
            ],
            'socialNetworks' => $socialNetworks,
            'sectionTypes' => BusinessMinisiteSection::getSectionTypes(),
        ]);
    }

    public function create(Request $request, Business $business)
    {
        $this->authorize('manageSections', $business);

        $galleries = BusinessGallery::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $forms = BusinessContactForm::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Minisite/Sections/Create', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'sectionTypes' => BusinessMinisiteSection::getSectionTypes(),
            'galleries' => $galleries,
            'forms' => $forms,
        ]);
    }

    public function store(Request $request, Business $business)
    {
        $this->authorize('manageSections', $business);

        $data = $request->validate([
            'section_type' => ['required', 'string'],
            'title' => ['nullable', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'config' => ['nullable', 'array'],
            'buttons' => ['nullable', 'array'],
            'buttons.*.text' => ['required', 'string', 'max:50'],
            'buttons.*.url' => ['required', 'string', 'max:255'],
            'buttons.*.style' => ['nullable', 'string', 'in:primary,secondary,outline'],
            'is_active' => ['boolean'],
        ]);

        $typeCount = BusinessMinisiteSection::where('business_id', $business->id)
            ->where('section_type', $data['section_type'])
            ->count();

        $sectionKey = $data['section_type'] . '_' . ($typeCount + 1);

        $maxOrder = BusinessMinisiteSection::where('business_id', $business->id)->max('sort_order') ?? 0;

        $section = BusinessMinisiteSection::create([
            'business_id' => $business->id,
            'section_type' => $data['section_type'],
            'section_key' => $sectionKey,
            'title' => $data['title'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'config' => $data['config'] ?? BusinessMinisiteSection::getDefaultConfig($data['section_type']),
            'buttons' => $data['buttons'] ?? [],
            'sort_order' => $maxOrder + 1,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('member.businesses.minisite.sections.index', $business->id)
            ->with('success', 'Sección creada.');
    }

    public function edit(Request $request, Business $business, BusinessMinisiteSection $section)
    {
        $this->authorize('manageSection', $section);

        abort_unless($section->business_id === $business->id, 403);

        $galleries = BusinessGallery::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $forms = BusinessContactForm::where('business_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Minisite/Sections/Edit', [
            'business' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'section' => [
                'id' => $section->id,
                'section_type' => $section->section_type,
                'section_key' => $section->section_key,
                'title' => $section->title,
                'subtitle' => $section->subtitle,
                'description' => $section->description,
                'config' => $section->config,
                'buttons' => $section->buttons ?? [],
                'sort_order' => $section->sort_order,
                'is_active' => $section->is_active,
            ],
            'sectionTypes' => BusinessMinisiteSection::getSectionTypes(),
            'galleries' => $galleries,
            'forms' => $forms,
        ]);
    }

    public function update(Request $request, Business $business, BusinessMinisiteSection $section)
    {
        $this->authorize('manageSection', $section);

        abort_unless($section->business_id === $business->id, 403);

        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:150'],
            'subtitle' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'config' => ['nullable', 'array'],
            'buttons' => ['nullable', 'array'],
            'buttons.*.text' => ['required', 'string', 'max:50'],
            'buttons.*.url' => ['required', 'string', 'max:255'],
            'buttons.*.style' => ['nullable', 'string', 'in:primary,secondary,outline'],
            'is_active' => ['boolean'],
        ]);

        $section->update($data);

        return redirect()->back()->with('success', 'Sección actualizada.');
    }

    public function destroy(Request $request, Business $business, BusinessMinisiteSection $section)
    {
        $this->authorize('manageSection', $section);

        abort_unless($section->business_id === $business->id, 403);

        $section->delete();

        return redirect()->back()->with('success', 'Sección eliminada.');
    }

    public function reorder(Request $request, Business $business)
    {
        $this->authorize('manageSections', $business);

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:business_minisite_sections,id'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            BusinessMinisiteSection::where('id', $id)
                ->where('business_id', $business->id)
                ->update(['sort_order' => $index + 1]);
        }

        return redirect()->to("/member/businesses/{$business->id}/minisite/sections", 303);
    }

    private function getServicesData(Business $business, array $config): array
    {
        $query = $business->services()
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['service_ids'])) {
            $query->whereIn('id', $config['service_ids']);
        }

        $limit = 20;
        $query->limit($limit);

        return $query->get(['id', 'name', 'description', 'price', 'image'])->map(function ($service) {
            $image = $service->image;
            if ($image && !str_starts_with($image, 'http')) {
                $image = '/storage/' . $image;
            }
            return [
                'id' => $service->id,
                'name' => $service->name,
                'description' => $service->description,
                'price' => $service->price,
                'image' => $image,
            ];
        })->toArray();
    }

    private function getGalleryData(Business $business, array $config): array
    {
        $galleryId = $config['gallery_id'] ?? null;
        $limit = $config['images_limit'] ?? 10;

        $query = $business->galleryImages()
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

    private function getPromotionsData(Business $business, array $config): array
    {
        $query = $business->promotions()
            ->where('is_active', true);

        if (!empty($config['promotion_ids'])) {
            $query->whereIn('id', $config['promotion_ids']);
        }

        return $query
            ->orderBy('sort_order')
            ->get(['id', 'name', 'description', 'image', 'regular_price', 'promotion_price', 'expires_at', 'coupon_code'])
            ->map(function ($promo) {
                return [
                    'id' => $promo->id,
                    'name' => $promo->name,
                    'slug' => $promo->slug,
                    'description' => $promo->description,
                    'regular_price' => $promo->regular_price,
                    'promotion_price' => $promo->promotion_price,
                    'expires_at' => $promo->expires_at,
                    'image' => $promo->image,
                    'coupon_code' => $promo->coupon_code,
                ];
            })->toArray();
    }

    private function getContactFormData(Business $business, array $config): ?array
    {
        $formId = $config['form_id'] ?? null;

        if (!$formId) {
            return null;
        }

        $form = $business->contactForms()
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

    private function getLocationsData(Business $business, array $config): array
    {
        $query = $business->locations()
            ->where('is_active', true);

        if (!empty($config['location_ids'])) {
            $query->whereIn('id', $config['location_ids']);
        }

        return $query
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get(['id', 'name', 'address_line_1', 'city', 'state', 'state_code', 'country', 'phone', 'email', 'latitude', 'longitude', 'directions_url'])
            ->map(function ($location) {
                $statePart = $location->state ?: $location->state_code;
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
                ];
            })->toArray();
    }

    private function getAboutData(Business $business, array $config): ?array
    {
        return [
            'name' => $business->name,
            'description' => $business->description ?? '',
            'logo' => $business->logo,
            'image' => $business->image,
        ];
    }

    private function getFeaturesData(Business $business, array $config): array
    {
        $query = $business->features()
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

    private function getFaqsData(Business $business, array $config): array
    {
        $query = $business->faqs()
            ->where('is_active', true)
            ->whereNull('category_id');

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

    private function getProductsData(Business $business, array $config): array
    {
        $query = $business->products()
            ->where('is_active', true)
            ->orderBy('sort_order');

        if (!empty($config['product_ids'])) {
            $query->whereIn('id', $config['product_ids']);
        }

        return $query
            ->with('images')
            ->get(['id', 'name', 'description', 'price', 'compare_at_price'])
            ->map(function ($product) {
                $firstImage = null;
                if ($product->images && $product->images->isNotEmpty()) {
                    $firstImage = $product->images->first()->path;
                    if ($firstImage && !str_starts_with($firstImage, 'http')) {
                        $firstImage = '/storage/' . $firstImage;
                    }
                }

                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $product->price,
                    'compare_at_price' => $product->compare_at_price,
                    'image' => $firstImage,
                ];
            })->toArray();
    }

    private function getPackagesData(Business $business, array $config): array
    {
        $query = $business->packages()
            ->where('is_active', true)
            ->with('features')
            ->orderBy('sort_order');

        if (!empty($config['package_ids'])) {
            $query->whereIn('id', $config['package_ids']);
        }

        return $query
            ->get(['id', 'title', 'short_description', 'long_description', 'image', 'price', 'promo_price', 'whatsapp', 'whatsapp_message'])
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
