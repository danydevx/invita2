<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\HelpArticle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HelpArticleController extends Controller
{
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search', ''));
        $category = $request->input('category', '');

        $articles = HelpArticle::query()
            ->where('is_published', true)
            ->when($search !== '', function ($query) use ($search) {
                $needle = mb_strtolower($search);
                $query->where(function ($q) use ($needle) {
                    $q->whereRaw('LOWER(title) like ?', ['%'.$needle.'%'])
                        ->orWhereRaw('LOWER(excerpt) like ?', ['%'.$needle.'%'])
                        ->orWhereRaw('LOWER(content) like ?', ['%'.$needle.'%']);
                });
            })
            ->when($category !== '', fn ($query) => $query->where('category', $category))
            ->orderByRaw('sort_order is null, sort_order asc')
            ->orderByDesc('published_at')
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($article) => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'category' => $article->category,
                'published_at' => $article->published_at?->toDateString(),
            ]);

        $categories = HelpArticle::query()
            ->where('is_published', true)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values();

        return Inertia::render('Member/Help/Index', [
            'articles' => $articles,
            'categories' => $categories,
            'filters' => [
                'search' => $search,
                'category' => $category,
            ],
        ]);
    }

    public function show(string $slug)
    {
        $article = HelpArticle::query()
            ->where('is_published', true)
            ->where('slug', $slug)
            ->firstOrFail();

        return Inertia::render('Member/Help/Show', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'content' => $article->content,
                'category' => $article->category,
                'published_at' => $article->published_at?->toDateString(),
            ],
        ]);
    }
}
