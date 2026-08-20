<?php

namespace Modules\ListingMinisite\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingGallery\Models\ListingGallery;
use Modules\ListingMinisite\Models\ListingMinisiteSection;
use Modules\ListingMinisite\Models\ListingMinisiteSetting;
use Modules\ListingContactForm\Models\ListingContactForm;

class ListingMinisiteSectionController extends Controller
{
    public function index(Request $request, Listing $listing)
    {
        $this->authorize('manageSections', $listing);

        $sections = ListingMinisiteSection::where('listing_id', $listing->id)
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
                    'config' => $config,
                    'buttons' => $section->buttons ?? [],
                    'sort_order' => $section->sort_order,
                    'is_active' => $section->is_active,
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
                    case 'packages':
                        $sectionData['items'] = $this->getPackagesData($listing, $config);
                        break;
                }

                return $sectionData;
            });

        $setting = ListingMinisiteSetting::where('listing_id', $listing->id)->first();

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
        if (class_exists('\Modules\ListingSocialMedia\Models\ListingSocialNetwork')) {
            $socialNetworks = \Modules\ListingSocialMedia\Models\ListingSocialNetwork::where('listing_id', $listing->id)
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
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
                'slug' => $listing->slug,
                'logo' => $listing->logo,
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
            'sectionTypes' => ListingMinisiteSection::getSectionTypes(),
        ]);
    }

    public function create(Request $request, Listing $listing)
    {
        $this->authorize('manageSections', $listing);

        $galleries = ListingGallery::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $forms = ListingContactForm::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Minisite/Sections/Create', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'sectionTypes' => ListingMinisiteSection::getSectionTypes(),
            'galleries' => $galleries,
            'forms' => $forms,
        ]);
    }

    public function store(Request $request, Listing $listing)
    {
        $this->authorize('manageSections', $listing);

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

        $typeCount = ListingMinisiteSection::where('listing_id', $listing->id)
            ->where('section_type', $data['section_type'])
            ->count();

        $sectionKey = $data['section_type'] . '_' . ($typeCount + 1);

        $maxOrder = ListingMinisiteSection::where('listing_id', $listing->id)->max('sort_order') ?? 0;

        $section = ListingMinisiteSection::create([
            'listing_id' => $listing->id,
            'section_type' => $data['section_type'],
            'section_key' => $sectionKey,
            'title' => $data['title'] ?? null,
            'subtitle' => $data['subtitle'] ?? null,
            'description' => $data['description'] ?? null,
            'config' => $data['config'] ?? ListingMinisiteSection::getDefaultConfig($data['section_type']),
            'buttons' => $data['buttons'] ?? [],
            'sort_order' => $maxOrder + 1,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return redirect()->route('member.listings.minisite.sections.index', $listing->id)
            ->with('success', 'Sección creada.');
    }

    public function edit(Request $request, Listing $listing, ListingMinisiteSection $section)
    {
        $this->authorize('manageSection', $section);

        abort_unless($section->listing_id === $listing->id, 403);

        $galleries = ListingGallery::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $forms = ListingContactForm::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Minisite/Sections/Edit', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
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
            'sectionTypes' => ListingMinisiteSection::getSectionTypes(),
            'galleries' => $galleries,
            'forms' => $forms,
        ]);
    }

    public function update(Request $request, Listing $listing, ListingMinisiteSection $section)
    {
        $this->authorize('manageSection', $section);

        abort_unless($section->listing_id === $listing->id, 403);

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

    public function destroy(Request $request, Listing $listing, ListingMinisiteSection $section)
    {
        $this->authorize('manageSection', $section);

        abort_unless($section->listing_id === $listing->id, 403);

        $section->delete();

        return redirect()->back()->with('success', 'Sección eliminada.');
    }

    public function reorder(Request $request, Listing $listing)
    {
        $this->authorize('manageSections', $listing);

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', 'exists:listing_minisite_sections,id'],
        ]);

        foreach ($data['ids'] as $index => $id) {
            ListingMinisiteSection::where('id', $id)
                ->where('listing_id', $listing->id)
                ->update(['sort_order' => $index + 1]);
        }

        return redirect()->to("/member/listings/{$listing->id}/minisite/sections", 303);
    }

    private function getServicesData(Listing $listing, array $config): array
    {
        $query = $listing->services()
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

    private function getLocationsData(Listing $listing, array $config): array
    {
        $query = $listing->locations()
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

    private function getAboutData(Listing $listing, array $config): ?array
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

    private function getProductsData(Listing $listing, array $config): array
    {
        $query = $listing->products()
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
