<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingServices\Models\ListingServiceCategory;
use Illuminate\Support\Facades\Auth;

class ServiceCategoryController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');

        $query = ListingServiceCategory::where('listing_id', $business->id)
            ->with('services')
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name');

        $categories = $query->paginate($perPage);

        $formattedCategories = $categories->getCollection()->map(function ($cat) {
            return [
                'id' => $cat->id,
                'name' => $cat->name,
                'description' => $cat->description,
                'slug' => $cat->slug,
                'is_active' => $cat->is_active,
                'sort_order' => $cat->sort_order,
                'services_count' => $cat->services()->count(),
            ];
        });

        $dataTable = [
            'data' => $formattedCategories->toArray(),
            'current_page' => $categories->currentPage(),
            'last_page' => $categories->lastPage(),
            'per_page' => $categories->perPage(),
            'total' => $categories->total(),
            'from' => $categories->firstItem(),
            'to' => $categories->lastItem(),
        ];

        $allCategories = ListingServiceCategory::where('listing_id', $business->id)
            ->orderBy('name')
            ->get();

        return Inertia::render('Member/Services/Categories/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'categories' => $allCategories,
            'dataTable' => $dataTable,
        ]);
    }

    public function store(Request $request, Listing $business)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $validated['listing_id'] = $business->id;
        $validated['slug'] = ListingServiceCategory::generateUniqueSlug($business->id, $validated['name']);

        ListingServiceCategory::create($validated);

        return redirect()->back()->with('success', 'Categoría creada exitosamente.');
    }

    public function update(Request $request, Listing $business, ListingServiceCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->listing_id === $business->id, 403);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Categoría actualizada exitosamente.');
    }

    public function destroy(Listing $business, ListingServiceCategory $category)
    {
        $user = Auth::user();
        abort_unless($business->user_id === $user->id, 403);
        abort_unless($category->listing_id === $business->id, 403);

        $category->services()->update(['category_id' => null]);

        $category->delete();

        return redirect()->back()->with('success', 'Categoría eliminada. Los servicios fueron desvinculados.');
    }
}
