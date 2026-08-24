<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Listings\Models\Listing;
use Modules\ListingFaqs\Models\ListingFaq;
use Modules\ListingFaqs\Models\ListingFaqCategory;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
    public function index(Request $request, Listing $business)
    {
        $this->authorize('viewAny', [ListingFaq::class, $business]);

        $perPage = min((int) $request->get('per_page', 10), 100);
        $search = $request->get('search', '');
        $categoryId = $request->get('category');
        $sort = $request->get('sort', 'sort_order');
        $direction = $request->get('direction', 'asc');

        $allowedSorts = ['question', 'answer', 'is_active', 'sort_order', 'created_at'];
        if (!in_array($sort, $allowedSorts)) {
            $sort = 'sort_order';
        }
        $direction = strtolower($direction) === 'desc' ? 'desc' : 'asc';

        $query = $business->faqs()
            ->with('category')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('question', 'like', "%{$search}%")
                      ->orWhere('answer', 'like', "%{$search}%");
                });
            })
            ->when($categoryId, function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            })
            ->orderBy($sort, $direction)
            ->orderBy('question');

        $faqs = $query->paginate($perPage);

        $categories = ListingFaqCategory::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        $dataTable = [
            'data' => collect($faqs->items())->map(function ($faq) {
                return [
                    'id' => $faq->id,
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category_id' => $faq->category_id,
                    'category' => $faq->category ? [
                        'id' => $faq->category->id,
                        'name' => $faq->category->name,
                    ] : null,
                    'is_active' => $faq->is_active,
                    'sort_order' => $faq->sort_order,
                    'created_at' => $faq->created_at,
                ];
            })->toArray(),
            'current_page' => $faqs->currentPage(),
            'last_page' => $faqs->lastPage(),
            'per_page' => $faqs->perPage(),
            'total' => $faqs->total(),
            'from' => $faqs->firstItem(),
            'to' => $faqs->lastItem(),
        ];

        return Inertia::render('Member/Faqs/Index', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'categories' => $categories,
            'dataTable' => $dataTable,
            'filters' => [
                'category' => $categoryId,
            ],
        ]);
    }

    public function create(Request $request, Listing $business)
    {
        $this->authorize('create', [ListingFaq::class, $business]);

        $categories = ListingFaqCategory::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Faqs/Create', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'categories' => $categories,
        ]);
    }

    public function store(Request $request, Listing $business, ActivityService $activity)
    {
        $this->authorize('create', [ListingFaq::class, $business]);

        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:listing_faq_categories,id'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,webp', 'max:10240'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['listing_id'] = $business->id;

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('faqs/' . $business->id, ['disk' => 'public']);
            $data['image'] = Storage::disk('public')->url($path);
        }

        $faq = ListingFaq::create($data);

        $activity->log('faq_created', [
            'actor' => $request->user(),
            'subject' => $faq,
            'description' => 'Pregunta frecuente creada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.faqs.index', $business->id)
            ->with('success', 'Pregunta frecuente creada correctamente.');
    }

    public function edit(Request $request, Listing $business, ListingFaq $faq)
    {
        $this->authorize('update', [ListingFaq::class, $faq]);

        $categories = ListingFaqCategory::where('listing_id', $business->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('Member/Faqs/Edit', [
            'listing' => [
                'id' => $business->id,
                'name' => $business->name,
            ],
            'faq' => [
                'id' => $faq->id,
                'question' => $faq->question,
                'answer' => $faq->answer,
                'category_id' => $faq->category_id,
                'image' => $faq->image,
                'is_active' => $faq->is_active,
                'sort_order' => $faq->sort_order,
            ],
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Listing $business, ListingFaq $faq, ActivityService $activity)
    {
        $this->authorize('update', [ListingFaq::class, $faq]);

        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:listing_faq_categories,id'],
            'image' => ['nullable', 'file', 'mimes:jpeg,png,webp', 'max:10240'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->hasFile('image')) {
            if ($faq->image) {
                $oldPath = str_replace(url('/') . '/storage/', '', $faq->image);
                Storage::disk('public')->delete($oldPath);
            }
            $path = $request->file('image')->store('faqs/' . $business->id, ['disk' => 'public']);
            $data['image'] = Storage::disk('public')->url($path);
        } else {
            unset($data['image']);
        }

        $faq->update($data);

        $activity->log('faq_updated', [
            'actor' => $request->user(),
            'subject' => $faq,
            'description' => 'Pregunta frecuente actualizada',
            'request' => $request,
        ]);

        return redirect()->route('member.listings.faqs.index', $business->id)
            ->with('success', 'Pregunta frecuente actualizada correctamente.');
    }

    public function destroy(Request $request, Listing $business, ListingFaq $faq, ActivityService $activity)
    {
        $this->authorize('delete', [ListingFaq::class, $faq]);

        if ($faq->image) {
            $oldPath = str_replace(url('/') . '/storage/', '', $faq->image);
            Storage::disk('public')->delete($oldPath);
        }

        $activity->log('faq_deleted', [
            'actor' => $request->user(),
            'subject' => $faq,
            'description' => 'Pregunta frecuente eliminada',
        ]);

        $faq->delete();

        return redirect()->route('member.listings.faqs.index', $business->id)
            ->with('success', 'Pregunta frecuente eliminada correctamente.');
    }

    public function clone(Request $request, Listing $business, ListingFaq $faq, ActivityService $activity)
    {
        $this->authorize('create', [ListingFaq::class, $business]);

        $maxSortOrder = ListingFaq::where('listing_id', $business->id)->max('sort_order') ?? 0;

        $clonedFaq = ListingFaq::create([
            'listing_id' => $business->id,
            'question' => $faq->question . ' (copia)',
            'answer' => $faq->answer,
            'category_id' => $faq->category_id,
            'is_active' => false,
            'sort_order' => $maxSortOrder + 1,
        ]);

        $activity->log('faq_cloned', [
            'actor' => $request->user(),
            'subject' => $clonedFaq,
            'description' => 'Pregunta frecuente clonada',
        ]);

        return redirect()->route('member.listings.faqs.index', $business->id)
            ->with('success', 'Pregunta frecuente clonada correctamente.');
    }

    public function bulkDelete(Request $request, Listing $business)
    {
        $this->authorize('deleteAny', [\Modules\ListingFaqs\Models\ListingFaq::class, $business]);

        $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('business_faqs', 'id')->where('listing_id', $business->id)],
        ]);

        $count = \Modules\ListingFaqs\Models\ListingFaq::where('listing_id', $business->id)
            ->whereIn('id', $request->ids)
            ->delete();

        return redirect()->back()->with('success', $count . ' pregunta(s) eliminada(s).');
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
            'ids.*' => ['integer', \Illuminate\Validation\Rule::exists('listing_faqs', 'id')->where('listing_id', $business->id)],
            'page' => ['nullable', 'integer', 'min:1'],
            'perPage' => ['nullable', 'integer', 'min:1'],
        ]);

        $page = $data['page'] ?? 1;
        $perPage = $data['perPage'] ?? count($data['ids']);
        $start = (($page - 1) * $perPage) + 1;

        \DB::transaction(function () use ($data, $business, $start) {
            foreach ($data['ids'] as $index => $id) {
                \Modules\ListingFaqs\Models\ListingFaq::where('id', $id)
                    ->where('listing_id', $business->id)
                    ->update(['sort_order' => $start + $index]);
            }
        });

        return back(303);
    }
}
