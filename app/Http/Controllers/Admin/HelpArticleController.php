<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class HelpArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $status = $request->input('status', '');
        $category = $request->input('category', '');

        $articles = HelpArticle::query()
            ->when($search !== '', function ($query) use ($search) {
                $needle = mb_strtolower($search);
                $query->where(function ($q) use ($needle) {
                    $q->whereRaw('LOWER(title) like ?', ['%'.$needle.'%'])
                        ->orWhereRaw('LOWER(slug) like ?', ['%'.$needle.'%']);
                });
            })
            ->when($status !== '', function ($query) use ($status) {
                $query->where('is_published', $status === 'published');
            })
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($article) => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'category' => $article->category,
                'is_published' => (bool) $article->is_published,
                'published_at' => $article->published_at?->toDateString(),
                'updated_at' => $article->updated_at?->toDateString(),
            ]);

        $categories = HelpArticle::query()
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values();

        return Inertia::render('Admin/Help/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'status' => $status,
                'category' => $category,
            ],
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Help/Create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $article = HelpArticle::create([
            'title' => trim($data['title']),
            'slug' => trim($data['slug']),
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'category' => $data['category'] ?? null,
            'is_published' => (bool) ($data['is_published'] ?? false),
            'sort_order' => $data['sort_order'] ?? null,
            'published_at' => $this->resolvePublishedAt($data),
        ]);

        return redirect()->route('admin.help.edit', $article);
    }

    public function edit(HelpArticle $article)
    {
        return Inertia::render('Admin/Help/Edit', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'content' => $article->content,
                'category' => $article->category,
                'is_published' => (bool) $article->is_published,
                'sort_order' => $article->sort_order,
                'published_at' => $article->published_at?->toDateString(),
            ],
        ]);
    }

    public function update(Request $request, HelpArticle $article)
    {
        $data = $this->validated($request, $article->id);

        $article->update([
            'title' => trim($data['title']),
            'slug' => trim($data['slug']),
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $data['content'],
            'category' => $data['category'] ?? null,
            'is_published' => (bool) ($data['is_published'] ?? false),
            'sort_order' => $data['sort_order'] ?? null,
            'published_at' => $this->resolvePublishedAt($data, $article->published_at),
        ]);

        return redirect()->route('admin.help.edit', $article)->with('success', 'Articulo actualizado correctamente.');
    }

    public function destroy(HelpArticle $article)
    {
        $article->delete();

        return redirect()->route('admin.help.index');
    }

    private function validated(Request $request, ?int $articleId = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:200', Rule::unique('help_articles', 'slug')->ignore($articleId)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'category' => ['nullable', 'string', 'max:100'],
            'is_published' => ['boolean'],
            'published_at' => ['nullable', 'date'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);
    }

    private function resolvePublishedAt(array $data, $current = null)
    {
        if (! empty($data['published_at'])) {
            return $data['published_at'];
        }

        if (! empty($data['is_published'])) {
            return $current ?? now();
        }

        return null;
    }
}
