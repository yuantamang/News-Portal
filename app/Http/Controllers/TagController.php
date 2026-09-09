<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Posts per page (fills three full rows at the current lg:grid-cols-3
     * breakpoint).
     */
    private const PER_PAGE = 9;

    public function show(Tag $tag): View
    {
        $posts = $tag->posts()
            ->published()
            ->with('categories')
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate(self::PER_PAGE)
            ->withQueryString();

        return view('tag', [
            'tag' => $tag,
            'posts' => $posts,
        ]);
    }
}
