<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingPackages\Models\ListingPackage;
use Modules\ListingPackages\Models\PackageFeature;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingPackage::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['title', 'price', 'promo_price', 'is_active', 'sort_order', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->packages()
            ->with('features')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('short_description', 'like', "%{$search}%");
                });
            })
            ->orderBy($sort, $direction)
            ->orderBy('title');

        $packages = $query->paginate($perPage);

        $dataTable = [
            'data' => collect($packages->items())->map(function ($package) {
                return [
                    'id' => $package->id,
                    'title' => $package->title,
                    'short_description' => $package->short_description,
                    'price' => $package->price,
                    'promo_price' => $package->promo_price,
                    'image' => $package->image,
                    'is_active' => $package->is_active,
                    'sort_order' => $package->sort_order,
                    'features_count' => $package->features->count(),
                    'created_at' => $package->created_at,
                ];
            })->toArray(),
            'current_page' => $packages->currentPage(),
            'last_page' => $packages->lastPage(),
            'per_page' => $packages->perPage(),
            'total' => $packages->total(),
            'from' => $packages->firstItem(),
            'to' => $packages->lastItem(),
        ];

        return Inertia::render('Member/Packages/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'dataTable' => $dataTable,
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingPackage::class, $business]);

        return Inertia::render('Member/Packages/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'defaultWhatsapp' => $business->phone ?? '',
            'defaultWhatsappMessage' => 'Hola, me interesa el paquete {package_title}',
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingPackage::class, $business]);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'short_description' => ['required', 'string', 'max:255'],
            'long_description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'promo_price' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'whatsapp_message' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:150'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'title' => 'título',
            'short_description' => 'descripción corta',
            'long_description' => 'descripción larga',
            'image' => 'imagen',
            'price' => 'precio',
            'promo_price' => 'precio promocional',
            'whatsapp' => 'whatsapp',
            'whatsapp_message' => 'mensaje de whatsapp',
            'is_active' => 'activo',
            'sort_order' => 'orden',
        ]);

        $data['listing_id'] = $business->id;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('packages/' . $business->id, ['disk' => 'public']);
            $data['image'] = Storage::disk('public')->url($path);
        }

        if (!isset($data['sort_order'])) {
            $data['sort_order'] = ListingPackage::where('listing_id', $business->id)->max('sort_order') + 1;
        }

        $features = $data['features'] ?? [];
        unset($data['features']);

        $package = ListingPackage::create($data);

        foreach ($features as $index => $featureName) {
            if (!empty(trim($featureName))) {
                PackageFeature::create([
                    'package_id' => $package->id,
                    'name' => trim($featureName),
                    'sort_order' => $index,
                ]);
            }
        }

        $activity->log('package_created', [
            'actor' => $request->user(),
            'subject' => $package,
            'description' => 'Paquete creado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.packages.index', $business->id)
            ->with('success', 'Paquete creado correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingPackage $package)
    {
        $this->authorize('update', [ListingPackage::class, $package]);

        abort_unless($package->listing_id === $business->id, 404);

        $package->load('features');

        return Inertia::render('Member/Packages/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'package' => [
                'id' => $package->id,
                'title' => $package->title,
                'short_description' => $package->short_description,
                'long_description' => $package->long_description,
                'image' => $package->image,
                'price' => $package->price,
                'promo_price' => $package->promo_price,
                'whatsapp' => $package->whatsapp,
                'whatsapp_message' => $package->whatsapp_message,
                'is_active' => $package->is_active,
                'sort_order' => $package->sort_order,
                'features' => $package->features->map(fn($f) => $f->name)->toArray(),
            ],
            'defaultWhatsapp' => $business->phone ?? '',
            'defaultWhatsappMessage' => 'Hola, me interesa el paquete {package_title}',
        ]);
    }

    public function update(Request $request, Listing $business, ListingPackage $package, ActivityService $activity)
    {
        $this->authorize('update', [ListingPackage::class, $package]);

        abort_unless($package->listing_id === $business->id, 404);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:150'],
            'short_description' => ['required', 'string', 'max:255'],
            'long_description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png', 'max:2048'],
            'price' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'promo_price' => ['nullable', 'numeric', 'min:0', 'max:999999999.99'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'whatsapp_message' => ['nullable', 'string'],
            'features' => ['nullable', 'array'],
            'features.*' => ['nullable', 'string', 'max:150'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ], [], [
            'title' => 'título',
            'short_description' => 'descripción corta',
            'long_description' => 'descripción larga',
            'image' => 'imagen',
            'price' => 'precio',
            'promo_price' => 'precio promocional',
            'whatsapp' => 'whatsapp',
            'whatsapp_message' => 'mensaje de whatsapp',
            'is_active' => 'activo',
            'sort_order' => 'orden',
        ]);

        if ($request->hasFile('image')) {
            if ($package->image) {
                $oldPath = str_replace(url('/') . '/storage/', '', $package->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('packages/' . $business->id, ['disk' => 'public']);
            $data['image'] = Storage::disk('public')->url($path);
        } else {
            unset($data['image']);
        }

        $features = $data['features'] ?? [];
        unset($data['features']);

        $package->update($data);

        $package->features()->delete();
        foreach ($features as $index => $featureName) {
            if (!empty(trim($featureName))) {
                PackageFeature::create([
                    'package_id' => $package->id,
                    'name' => trim($featureName),
                    'sort_order' => $index,
                ]);
            }
        }

        $activity->log('package_updated', [
            'actor' => $request->user(),
            'subject' => $package,
            'description' => 'Paquete actualizado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.packages.index', $business->id)
            ->with('success', 'Paquete actualizado correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingPackage $package, ActivityService $activity)
    {
        $this->authorize('delete', [ListingPackage::class, $package]);

        abort_unless($package->listing_id === $business->id, 404);

        if ($package->image) {
            $oldPath = str_replace(url('/') . '/storage/', '', $package->image);
            Storage::disk('public')->delete($oldPath);
        }

        $package->features()->delete();

        $activity->log('package_deleted', [
            'actor' => $request->user(),
            'subject' => $package,
            'description' => 'Paquete eliminado',
        ]);

        $package->delete();

        return redirect()->route('member.listings.packages.index', $business->id)
            ->with('success', 'Paquete eliminado correctamente.');
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $this->authorize('deleteAny', [ListingPackage::class, $business]);

        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('business_packages', 'id')->where('listing_id', $business->id)],
        ]);

        $packages = ListingPackage::where('listing_id', $business->id)
            ->whereIn('id', $request->ids)
            ->with('features')
            ->get();

        foreach ($packages as $package) {
            if ($package->image) {
                $oldPath = str_replace(url('/') . '/storage/', '', $package->image);
                Storage::disk('public')->delete($oldPath);
            }
            $package->features()->delete();
        }

        $count = ListingPackage::where('listing_id', $business->id)
            ->whereIn('id', $request->ids)
            ->delete();

        return redirect()->back()->with('success', $count . ' paquete(s) eliminado(s).');
    }

    public function reorder(Request $request, Listing $business)
    {
        $user = $request->user();

        if ($user->hasAnyRole(['superadmin', 'admin'])) {
            // allowed
        } else {
            abort_unless($business->user_id === $user->id, 403);
        }

        $data = $request->validate([
            'ids' => ['required', 'array'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('business_packages', 'id')->where('listing_id', $business->id)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        \DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                ListingPackage::where('id', $id)
                    ->where('listing_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }

    public function clone(Request $request, Listing $business, ListingPackage $package, ActivityService $activity)
    {
        $this->authorize('create', [ListingPackage::class, $business]);

        $maxOrder = $business->packages()->max('sort_order') ?? 0;

        $cloned = $business->packages()->create([
            'title' => $package->title . ' (copia)',
            'short_description' => $package->short_description,
            'long_description' => $package->long_description,
            'image' => $package->image,
            'price' => $package->price,
            'promo_price' => $package->promo_price,
            'whatsapp' => $package->whatsapp,
            'whatsapp_message' => $package->whatsapp_message,
            'is_active' => false,
            'sort_order' => $maxOrder + 1,
        ]);

        foreach ($package->features as $feature) {
            $cloned->features()->create([
                'name' => $feature->name,
                'sort_order' => $feature->sort_order,
            ]);
        }

        $activity->log('package_cloned', [
            'actor' => $request->user(),
            'subject' => $cloned,
            'description' => 'Paquete clonado',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.packages.edit', [$business->id, $cloned->id]);
    }
}
