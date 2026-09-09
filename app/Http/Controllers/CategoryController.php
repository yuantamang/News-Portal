<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Posts per page (fills three full rows at the current lg:grid-cols-3
     * breakpoint).
     */
    private const PER_PAGE = 9;

    public function show(Category $category): View
    {
        $posts = $category->posts()
            ->published()
            ->with('categories')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('category', [
            'category' => $category,
            'posts' => $posts,
        ]);
    }
}
