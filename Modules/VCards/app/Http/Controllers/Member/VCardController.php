<?php

namespace Modules\VCards\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

        $fieldTypes = VCardFieldType::getGrouped();
        $fieldTypeCategories = VCardFieldType::getCategories();
        $mostPopularFields = VCardFieldType::getMostPopular();

        return Inertia::render('Member/VCards/Create', [
            'listing' => $listing,
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

    public function store(Request $request, Listing $listing)
    {
        abort_unless($listing->user_id === Auth::id(), 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'type' => ['required', 'in:single,team'],
            'vcard_team_id' => ['nullable', 'exists:vcard_teams,id'],
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
            'design' => $validated['design'] ?? 'classic',
            'primary_color' => $validated['primary_color'] ?? '#2563EB',
            'font' => $validated['font'] ?? 'Inter',
            'prefix' => $validated['prefix'] ?? null,
            'first_name' => $validated['first_name'] ?? null,
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'accreditations' => $validated['accreditations'] ?? null,
            'preferred_name' => $validated['preferred_name'] ?? null,
            'pronouns' => $validated['pronouns'] ?? null,
            'title' => $validated['title'] ?? null,
            'department' => $validated['department'] ?? null,
            'company' => $validated['company'] ?? null,
            'headline' => $validated['headline'] ?? null,
            'logo' => $validated['logo'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'hero_background_image' => $validated['hero_background_image'] ?? null,
        ]);

        return redirect()->route('member.listings.vcards.edit', [$listing->id, $vcard->id])
            ->with('success', 'Tarjeta creada correctamente.');
    }

    public function edit(Listing $listing, VCard $vcard)
    {
        abort_unless($listing->user_id === Auth::id(), 403);
        abort_unless($vcard->listing_id === $listing->id, 403);

        $vcard->load(['contacts', 'fields']);

        $teams = \Modules\VCards\Models\VCardTeam::where('listing_id', $listing->id)
            ->active()
            ->orderBy('sort_order')
            ->get(['id', 'name']);

        $fieldTypes = VCardFieldType::getGrouped();
        $fieldTypeCategories = VCardFieldType::getCategories();
        $mostPopularFields = VCardFieldType::getMostPopular();

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
}
