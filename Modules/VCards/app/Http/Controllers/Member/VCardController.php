<?php

namespace Modules\VCards\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\VCards\Http\Requests\StoreVCardContactRequest;
use Modules\VCards\Http\Requests\StoreVCardFieldRequest;
use Modules\VCards\Http\Requests\UpdateVCardFieldRequest;
use Modules\VCards\Http\Resources\VCardResource;
use Modules\VCards\Models\VCard;
use Modules\VCards\Models\VCardContact;
use Modules\VCards\Models\VCardField;
use Modules\VCards\Models\VCardFieldType;
use Modules\VCards\Models\VCardSection;
use Modules\VCards\Models\VCardSelectedService;

class VCardController extends Controller
{
    public function index(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $perPage = min((int) $request->get('per_page', 15), 100);
        $search = $request->get('search', '');
        $teamId = $request->get('team_id');
        $sort = $request->get('sort', 'created_at');
        $direction = $request->get('direction', 'desc');

        $query = VCard::where('listing_id', $listing->id)
            ->with('team')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%");
            })
            ->when($teamId, function ($q) use ($teamId) {
                $q->where('vcard_team_id', $teamId);
            })
            ->orderBy($sort, $direction);

        $vcards = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($vcards->items())->map(function ($vcard) {
                return [
                    'id' => $vcard->id,
                    'name' => $vcard->name,
                    'slug' => $vcard->slug,
                    'type' => $vcard->type->value,
                    'active' => $vcard->active,
                    'views' => $vcard->views,
                    'team' => $vcard->team ? ['id' => $vcard->team->id, 'name' => $vcard->team->name] : null,
                    'profile_photo' => $vcard->profile_photo,
                    'created_at' => $vcard->created_at,
                ];
            })->toArray(),
            'current_page' => $vcards->currentPage(),
            'last_page' => $vcards->lastPage(),
            'per_page' => $vcards->perPage(),
            'total' => $vcards->total(),
            'from' => $vcards->firstItem(),
            'to' => $vcards->lastItem(),
        ];

        $teams = \Modules\VCards\Models\VCardTeam::where('listing_id', $listing->id)
            ->active()
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        return Inertia::render('Member/VCards/Index', [
            'listing' => [
                'id' => $listing->id,
                'name' => $listing->name,
            ],
            'dataTable' => $dataTable,
            'teams' => $teams,
            'filters' => [
                'search' => $search,
                'team_id' => $teamId,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function create(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $teams = \Modules\VCards\Models\VCardTeam::where('listing_id', $listing->id)
            ->active()
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        return Inertia::render('Member/VCards/Create', [
            'listing' => $listing,
            'teams' => $teams,
        ]);
    }

    public function store(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:single,team'],
            'vcard_team_id' => ['nullable', 'exists:vcard_teams,id'],
            'active' => ['boolean'],
            'search_engine_indexing' => ['boolean'],
            'renew' => ['boolean'],
            'tracking_code' => ['nullable', 'array'],
            'paused' => ['boolean'],
        ]);

        $slug = $validated['slug'] ?? Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;

        while (VCard::where('listing_id', $listing->id)->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $vcard = VCard::create([
            'listing_id' => $listing->id,
            'vcard_team_id' => $validated['vcard_team_id'] ?? null,
            'type' => $validated['type'],
            'name' => $validated['name'],
            'slug' => $slug,
            'active' => $validated['active'] ?? true,
            'design' => 'classic',
            'primary_color' => '#2563EB',
            'font' => 'Inter',
            'search_engine_indexing' => $validated['search_engine_indexing'] ?? true,
            'renew' => $validated['renew'] ?? true,
            'tracking_code' => $validated['tracking_code'] ?? [],
            'paused' => $validated['paused'] ?? false,
        ]);

        return redirect()->route('member.listings.vcards.edit', [$listing->id, $vcard->id])
            ->with('success', 'Tarjeta creada correctamente.');
    }

    public function edit(Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $vcard->load(['contacts', 'fields', 'sections', 'listing', 'listing.about', 'selectedServices.service', 'selectedPackages.package', 'selectedGallery.gallery.images', 'selectedProducts.product', 'selectedTestimonials.review', 'businessHours', 'selectedMenuCategories.category', 'selectedMenuCategories.category.activeProducts', 'selectedLocation', 'selectedFeatures.feature']);

        $teams = \Modules\VCards\Models\VCardTeam::where('listing_id', $listing->id)
            ->active()
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        $fieldTypes = VCardFieldType::getGrouped();
        $fieldTypeCategories = VCardFieldType::getCategories();
        $mostPopularFields = VCardFieldType::getMostPopular();

        $listing->load('about');

        return Inertia::render('Member/VCards/Edit', [
            'listing' => $listing,
            'vcard' => (new VCardResource($vcard))->resolve(request()),
            'teams' => $teams,
            'fieldTypes' => $fieldTypes,
            'fieldTypeCategories' => $fieldTypeCategories,
            'mostPopularFields' => $mostPopularFields,
            'designs' => array_map(fn($d) => ['value' => $d->value, 'label' => $d->label()], \Modules\VCards\Enums\VCardDesign::cases()),
            'fonts' => ['Inter', 'Roboto', 'Open Sans', 'Montserrat', 'Poppins', 'Lato', 'Nunito', 'Raleway', 'Merriweather', 'Playfair Display'],
            'colors' => ['#2563EB', '#4F46E5', '#7C3AED', '#DB2777', '#DC2626', '#EA580C', '#CA8A04', '#16A34A', '#0891B2', '#111827'],
            'contactTypes' => array_map(fn($c) => ['value' => $c->value, 'label' => $c->label()], \Modules\VCards\Enums\VCardContactType::cases()),
            'contactSubtypes' => array_map(fn($c) => ['value' => $c->value, 'label' => $c->label()], \Modules\VCards\Enums\VCardContactSubtype::cases()),
            'pronouns' => array_map(fn($c) => ['value' => $c->value, 'label' => $c->label()], \Modules\VCards\Enums\VCardPronouns::cases()),
        ]);
    }

    public function update(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:single,team'],
            'vcard_team_id' => ['nullable'],
            'active' => ['boolean'],
            'design' => ['in:classic,flat,modern,sleek,blend'],
            'primary_color' => ['string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'font' => ['string'],
            'prefix' => ['nullable', 'string', 'max:50'],
            'first_name' => ['nullable', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['nullable', 'string', 'max:100'],
            'accreditations' => ['nullable', 'string', 'max:255'],
            'preferred_name' => ['nullable', 'string', 'max:100'],
            'pronouns' => ['nullable', 'in:he,she,they'],
            'title' => ['nullable', 'string', 'max:255'],
            'department' => ['nullable', 'string', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'headline' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'string', 'max:500'],
            'badge' => ['nullable', 'string', 'max:500'],
            'hero_background_image' => ['nullable', 'string', 'max:500'],
            'shape' => ['nullable', 'in:circle,rounded'],
            'image_x' => ['nullable', 'numeric'],
            'image_y' => ['nullable', 'numeric'],
            'background_type' => ['nullable', 'in:solid,gradient,pattern'],
            'gradient_direction' => ['nullable', 'string', 'max:20'],
            'pattern_key' => ['nullable', 'string', 'max:50'],
            'hero_image_alpha' => ['nullable', 'integer', 'min:1', 'max:100'],
            'body_background_type' => ['nullable', 'in:solid,gradient,pattern'],
            'body_primary_color' => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'body_gradient_direction' => ['nullable', 'string', 'max:20'],
            'body_pattern_key' => ['nullable', 'string', 'max:50'],
            'search_engine_indexing' => ['boolean'],
            'renew' => ['boolean'],
            'tracking_code' => ['nullable', 'array'],
            'paused' => ['boolean'],
            'ai_chat_enabled' => ['boolean'],
            'meta_pixel_id' => ['nullable', 'string', 'max:255'],
            'google_analytics_id' => ['nullable', 'string', 'max:255'],
            'google_webmasters_verification' => ['nullable', 'string', 'max:255'],
            'bing_webmasters_verification' => ['nullable', 'string', 'max:255'],
        ]);

        if (isset($validated['slug']) && $validated['slug'] !== $vcard->slug) {
            $slug = $validated['slug'];
            $originalSlug = $slug;
            $counter = 1;

            while (VCard::where('listing_id', $listing->id)->where('slug', $slug)->where('id', '!=', $vcard->id)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }
            $validated['slug'] = $slug;
        } else {
            unset($validated['slug']);
        }

        $vcard->update($validated);

        return redirect()->back();
    }

    public function destroy(Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $vcard->delete();

        return redirect()->route('member.listings.vcards.index', [$listing->id]);
    }

    public function duplicate(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $newVCard = $vcard->replicate();
        $newVCard->name = $vcard->name . ' (copia)';
        $newVCard->slug = VCard::generateUniqueSlug($vcard->name . '-copia', $listing->id);
        $newVCard->views = 0;
        $newVCard->save();

        foreach ($vcard->contacts as $contact) {
            $newContact = $contact->replicate();
            $newContact->vcard_id = $newVCard->id;
            $newContact->save();
        }

        foreach ($vcard->fields as $field) {
            $newField = $field->replicate();
            $newField->vcard_id = $newVCard->id;
            $newField->save();
        }

        return redirect()->route('member.listings.vcards.edit', [$listing->id, $newVCard->id])
            ->with('success', 'Tarjeta duplicada correctamente.');
    }

    public function toggleActive(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $vcard->update(['active' => !$vcard->active]);

        return redirect()->back();
    }

    public function download(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $vcard->load(['contacts', 'activeFields']);

        $content = $vcard->toVCardString();
        $filename = Str::slug($vcard->name) . '.vcf';

        return response($content, 200, [
            'Content-Type' => 'text/vcard',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function reorder(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['exists:vcards,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            VCard::where('id', $id)->update(['sort_order' => $index]);
        }

        return redirect()->back();
    }

    public function bulkDelete(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['exists:vcards,id'],
        ]);

        VCard::where('listing_id', $listing->id)
            ->whereIn('id', $validated['ids'])
            ->delete();

        return redirect()->back()->with('success', 'Tarjetas eliminadas correctamente.');
    }

    public function storeContact(StoreVCardContactRequest $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validated();
        $validated['vcard_id'] = $vcard->id;

        $maxOrder = VCardContact::where('vcard_id', $vcard->id)->max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        VCardContact::create($validated);

        return redirect()->back()->with('success', 'Contacto agregado correctamente.');
    }

    public function updateContact(Request $request, Listing $listing, VCard $vcard, VCardContact $contact)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);
        abort_unless($contact->vcard_id === $vcard->id, 403);

        $validated = $request->validate([
            'type' => ['required', 'in:phone,email,whatsapp'],
            'contact_type' => ['in:personal,work,home'],
            'country_code' => ['nullable', 'string', 'max:10'],
            'value' => ['required', 'string', 'max:255'],
            'extension' => ['nullable', 'string', 'max:20'],
        ]);

        $contact->update($validated);

        return redirect()->back()->with('success', 'Contacto actualizado correctamente.');
    }

    public function destroyContact(Request $request, Listing $listing, VCard $vcard, VCardContact $contact)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);
        abort_unless($contact->vcard_id === $vcard->id, 403);

        $contact->delete();

        return redirect()->back()->with('success', 'Contacto eliminado correctamente.');
    }

    public function reorderContacts(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['exists:vcard_contacts,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            VCardContact::where('id', $id)->update(['sort_order' => $index]);
        }

        return redirect()->back();
    }

    public function storeField(StoreVCardFieldRequest $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validated();
        $validated['vcard_id'] = $vcard->id;

        if ($request->hasFile('config_file')) {
            $file = $request->file('config_file');
            $path = $file->store('vcard-fields', 'public');
            $validated['config']['file'] = $path;
        }

        $maxOrder = VCardField::where('vcard_id', $vcard->id)->max('sort_order') ?? 0;
        $validated['sort_order'] = $maxOrder + 1;

        VCardField::create($validated);

        return redirect()->back()->with('success', 'Campo agregado correctamente.');
    }

    public function updateField(UpdateVCardFieldRequest $request, Listing $listing, VCard $vcard, VCardField $field)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);
        abort_unless($field->vcard_id === $vcard->id, 403);

        $validated = $request->validated();

        if ($request->hasFile('config_file')) {
            $oldConfig = $field->config;
            if (!empty($oldConfig['file'])) {
                Storage::disk('public')->delete($oldConfig['file']);
            }
            $file = $request->file('config_file');
            $path = $file->store('vcard-fields', 'public');
            $validated['config']['file'] = $path;
        }

        $field->update($validated);

        return redirect()->back()->with('success', 'Campo actualizado correctamente.');
    }

    public function destroyField(Request $request, Listing $listing, VCard $vcard, VCardField $field)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);
        abort_unless($field->vcard_id === $vcard->id, 403);

        $field->delete();

        return redirect()->back()->with('success', 'Campo eliminado correctamente.');
    }

    public function reorderFields(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['exists:vcard_fields,id'],
        ]);

        foreach ($validated['order'] as $index => $id) {
            VCardField::where('id', $id)->update(['sort_order' => $index]);
        }

        return redirect()->back();
    }

    public function updateSections(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'sections' => ['required', 'array'],
            'sections.*.key' => ['required', 'string'],
            'sections.*.enabled' => ['required', 'boolean'],
        ]);

        foreach ($validated['sections'] as $section) {
            VCardSection::updateOrCreate(
                ['vcard_id' => $vcard->id, 'section_key' => $section['key']],
                ['enabled' => $section['enabled']]
            );
        }

        return redirect()->back()->with('success', 'Secciones actualizadas correctamente.');
    }

    public function updateSelectedServices(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'service_ids' => ['required', 'array'],
            'service_ids.*' => ['exists:listing_services,id'],
        ]);

        $vcard->selectedServices()->delete();

        foreach ($validated['service_ids'] as $index => $serviceId) {
            VCardSelectedService::create([
                'vcard_id' => $vcard->id,
                'service_id' => $serviceId,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Servicios actualizados correctamente.']);
    }

    public function updateSelectedPackages(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'package_ids' => ['required', 'array'],
            'package_ids.*' => ['exists:listing_packages,id'],
        ]);

        $vcard->selectedPackages()->delete();

        foreach ($validated['package_ids'] as $index => $packageId) {
            \Modules\VCards\Models\VCardSelectedPackage::create([
                'vcard_id' => $vcard->id,
                'package_id' => $packageId,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Paquetes actualizados correctamente.']);
    }

    public function getListingPackages(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $packages = \Modules\ListingPackages\Models\ListingPackage::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with('features')
            ->get(['id', 'title', 'short_description', 'price', 'promo_price']);

        return response()->json(['packages' => $packages]);
    }

    public function getListingServices(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $services = \Modules\ListingServices\Models\ListingService::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'name', 'price', 'duration_minutes', 'description']);

        return response()->json(['services' => $services]);
    }

    public function updateSelectedGallery(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'gallery_id' => ['nullable', 'exists:listing_galleries,id'],
        ]);

        if ($validated['gallery_id']) {
            \Modules\VCards\Models\VCardSelectedGallery::updateOrCreate(
                ['vcard_id' => $vcard->id],
                ['gallery_id' => $validated['gallery_id']]
            );
        } else {
            $vcard->selectedGallery()?->delete();
        }

        return response()->json(['success' => true, 'message' => 'Galeria actualizada correctamente.']);
    }

    public function getListingGalleries(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $galleries = \Modules\ListingGallery\Models\ListingGallery::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with(['images' => function ($q) {
                $q->where('is_active', true)->orderBy('sort_order')->limit(4);
            }])
            ->get(['id', 'name', 'description']);

        return response()->json(['galleries' => $galleries]);
    }

    public function updateSelectedProducts(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'product_ids' => ['required', 'array'],
            'product_ids.*' => ['exists:listing_products,id'],
        ]);

        $vcard->selectedProducts()->delete();

        foreach ($validated['product_ids'] as $index => $productId) {
            \Modules\VCards\Models\VCardSelectedProduct::create([
                'vcard_id' => $vcard->id,
                'product_id' => $productId,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Productos actualizados correctamente.']);
    }

    public function getListingProducts(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $products = \Modules\ListingProducts\Models\ListingProduct::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->with('images')
            ->get(['id', 'name', 'description', 'price', 'image']);

        return response()->json(['products' => $products]);
    }

    public function updateSelectedTestimonials(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'review_ids' => ['required', 'array'],
            'review_ids.*' => ['exists:listing_reviews,id'],
        ]);

        $vcard->selectedTestimonials()->delete();

        foreach ($validated['review_ids'] as $index => $reviewId) {
            \Modules\VCards\Models\VCardSelectedTestimonial::create([
                'vcard_id' => $vcard->id,
                'review_id' => $reviewId,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Testimonios actualizados correctamente.']);
    }

    public function getListingTestimonials(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $reviews = \Modules\ListingReviews\Models\ListingReview::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get(['id', 'client_name', 'company', 'comment', 'rating', 'google_link']);

        return response()->json(['testimonials' => $reviews]);
    }

    public function updateBusinessHours(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'hours' => ['required', 'array'],
            'hours.*.day_of_week' => ['required', 'integer', 'between:0,6'],
            'hours.*.is_open' => ['required', 'boolean'],
            'hours.*.opening_time' => ['nullable', 'date_format:H:i'],
            'hours.*.closing_time' => ['nullable', 'date_format:H:i'],
            'hours.*.lunch_start_time' => ['nullable', 'date_format:H:i'],
            'hours.*.lunch_end_time' => ['nullable', 'date_format:H:i'],
        ]);

        $vcard->businessHours()->delete();

        foreach ($validated['hours'] as $index => $hour) {
            \Modules\VCards\Models\VCardBusinessHour::create([
                'vcard_id' => $vcard->id,
                'day_of_week' => $hour['day_of_week'],
                'is_open' => $hour['is_open'],
                'opening_time' => $hour['opening_time'] ?? '08:00',
                'closing_time' => $hour['closing_time'] ?? '18:00',
                'lunch_start_time' => $hour['lunch_start_time'] ?? null,
                'lunch_end_time' => $hour['lunch_end_time'] ?? null,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Horario actualizado correctamente.']);
    }

    public function getBusinessHours(Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $hours = $vcard->businessHours()->get()->map(fn($h) => [
            'id' => $h->id,
            'day_of_week' => $h->day_of_week,
            'day_name' => $h->day_name,
            'is_open' => $h->is_open,
            'opening_time' => $h->opening_time,
            'closing_time' => $h->closing_time,
            'lunch_start_time' => $h->lunch_start_time,
            'lunch_end_time' => $h->lunch_end_time,
        ]);

        return response()->json(['hours' => $hours]);
    }

    public function updateMenuCategories(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'categories' => ['required', 'array', 'max:6'],
            'categories.*.category_id' => ['required', 'exists:menu_categories,id'],
            'categories.*.product_ids' => ['required', 'array', 'max:5'],
            'categories.*.product_ids.*' => ['exists:menu_products,id'],
        ]);

        $vcard->selectedMenuCategories()->delete();

        foreach ($validated['categories'] as $index => $cat) {
            \Modules\VCards\Models\VCardSelectedMenuCategory::create([
                'vcard_id' => $vcard->id,
                'category_id' => $cat['category_id'],
                'product_ids' => $cat['product_ids'],
                'sort_order' => $index,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Menú actualizado correctamente.']);
    }

    public function getListingMenus(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $categories = \Modules\ListingRestaurantMenu\Entities\MenuCategory::where('listing_id', $listing->id)
            ->where('active', true)
            ->whereNull('parent_id')
            ->with(['children', 'activeProducts'])
            ->orderBy('sort_order')
            ->get(['id', 'title', 'description', 'parent_id']);

        $result = $categories->map(fn($cat) => [
            'id' => $cat->id,
            'title' => $cat->title,
            'description' => $cat->description,
            'parent_id' => $cat->parent_id,
            'products' => $cat->activeProducts->map(fn($p) => [
                'id' => $p->id,
                'title' => $p->title,
                'description' => $p->description,
                'price' => $p->price,
                'image' => $p->image,
            ])->values(),
        ]);

        return response()->json(['categories' => $result]);
    }

    public function updateLocation(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'location_id' => ['nullable', 'exists:listing_locations,id'],
        ]);

        $vcard->selectedLocation()->delete();

        if ($validated['location_id']) {
            \Modules\VCards\Models\VCardSelectedLocation::create([
                'vcard_id' => $vcard->id,
                'location_id' => $validated['location_id'],
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Ubicación actualizada correctamente.']);
    }

    public function getListingLocations(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $locations = \Modules\ListingLocations\Models\ListingLocation::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->orderBy('is_primary', 'desc')
            ->get(['id', 'name', 'address_line_1', 'city', 'state', 'country', 'latitude', 'longitude', 'phone']);

        return response()->json(['locations' => $locations]);
    }

    public function updateSelectedFeatures(Request $request, Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $validated = $request->validate([
            'feature_ids' => ['required', 'array'],
            'feature_ids.*' => ['exists:features,id'],
        ]);

        $vcard->selectedFeatures()->delete();

        foreach ($validated['feature_ids'] as $index => $featureId) {
            \Modules\VCards\Models\VCardSelectedFeature::create([
                'vcard_id' => $vcard->id,
                'feature_id' => $featureId,
                'sort_order' => $index,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Características actualizadas correctamente.']);
    }

    public function getListingFeatures(Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $features = \Modules\ListingFeatures\Models\ListingFeature::where('listing_id', $listing->id)
            ->where('is_active', true)
            ->with(['feature' => function ($q) {
                $q->where('is_active', true);
            }])
            ->orderBy('sort_order')
            ->get()
            ->map(fn($lf) => [
                'id' => $lf->feature_id,
                'title' => $lf->feature->title ?? null,
                'description' => $lf->feature->description ?? null,
                'icon' => $lf->feature->icon ?? null,
            ])
            ->filter();

        return response()->json(['features' => $features]);
    }
}
