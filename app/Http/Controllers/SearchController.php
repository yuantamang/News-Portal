<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    /**
     * Posts per page (fills three full rows at the current lg:grid-cols-3
     * breakpoint).
     */
    private const PER_PAGE = 9;

    public function index(Request $request): View
    {
        $query = trim((string) $request->query('q', ''));
        $categorySlug = $request->query('category');

        $posts = null;

        if ($query !== '') {
            $posts = Post::query()
                ->published()
                ->where(function ($searchQuery) use ($query): void {
                    $searchQuery
                        ->where('title', 'like', "%{$query}%")
                        ->orWhere('context', 'like', "%{$query}%");
                })
                ->when(
                    filled($categorySlug),
                    fn ($postsQuery) => $postsQuery->whereHas(
                        'categories',
                        fn ($categoriesQuery) => $categoriesQuery->where('slug', $categorySlug)
                    )
                )
                ->with('categories')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->paginate(self::PER_PAGE)
                ->withQueryString();
        }

        return view('search', [
            'query' => $query !== '' ? $query : null,
            'posts' => $posts,
        ]);
    }
}
