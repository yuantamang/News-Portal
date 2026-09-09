<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Number of posts in the "Latest" rail.
     */
    private const LATEST_COUNT = 5;

    /**
     * Number of posts in the "More stories" grid (one full row at the
     * current lg:grid-cols-4 breakpoint).
     */
    private const MORE_COUNT = 4;

    /**
     * Number of posts in the "Trending Now" grid.
     */
    private const TRENDING_COUNT = 4;

    /**
     * Number of posts in the "Most Read" ranked list.
     */
    private const POPULAR_COUNT = 5;

    public function index(): View
    {
        $featuredPost = Post::query()
            ->published()
            ->where('is_featured', true)
            ->with('categories')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->first();

        $excludedIds = collect([$featuredPost?->id])->filter();

        $latestPosts = Post::query()
            ->published()
            ->whereNotIn('id', $excludedIds)
            ->with('categories')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(self::LATEST_COUNT)
            ->get();

        $excludedIds = $excludedIds->merge($latestPosts->pluck('id'));

        $morePosts = Post::query()
            ->published()
            ->whereNotIn('id', $excludedIds)
            ->with('categories')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(self::MORE_COUNT)
            ->get();

        $trendingPosts = Post::query()
            ->published()
            ->where('is_trending', true)
            ->with('categories')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(self::TRENDING_COUNT)
            ->get();

        $popularPosts = Post::query()
            ->published()
            ->with('categories')
            ->orderByDesc('view_count')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit(self::POPULAR_COUNT)
            ->get();

        return view('index', [
            'featuredPost' => $featuredPost,
            'latestPosts' => $latestPosts,
            'morePosts' => $morePosts,
            'trendingPosts' => $trendingPosts,
            'popularPosts' => $popularPosts,
        ]);
    }
}
