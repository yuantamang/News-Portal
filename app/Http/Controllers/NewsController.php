<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\PostViews;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Number of posts shown in the "Related coverage" rail.
     */
    private const RELATED_COUNT = 3;

    /**
     * A repeat view from the same IP within this window does not bump the
     * denormalized `posts.view_count` counter again, though it is still
     * logged to `post_views`. The backend docs leave the exact
     * de-duplication rule undefined, so this is a first implementation
     * choice, not a stated backend requirement.
     */
    private const VIEW_DEDUPE_WINDOW_HOURS = 24;

    public function show(Request $request, string $slug): View
    {
        $post = Post::query()
            ->where('slug', $slug)
            ->published()
            ->with(['categories', 'tags', 'media'])
            ->firstOrFail();

        $this->recordView($post, $request);

        return view('show', [
            'post' => $post,
            'relatedPosts' => $this->relatedPosts($post),
        ]);
    }

    /**
     * No related-post algorithm exists on the backend. First implementation
     * (per the wiring spec): prefer other published posts sharing at least
     * one category, newest first, then fill any remaining slots with other
     * recent published posts so the rail isn't left sparse.
     *
     * @return Collection<int, Post>
     */
    private function relatedPosts(Post $post): Collection
    {
        $related = collect();

        if ($post->categories->isNotEmpty()) {
            $related = Post::query()
                ->published()
                ->whereKeyNot($post->id)
                ->whereHas('categories', function ($query) use ($post): void {
                    $query->whereIn('categories.id', $post->categories->pluck('id'));
                })
                ->with('categories')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(self::RELATED_COUNT)
                ->get();
        }

        if ($related->count() < self::RELATED_COUNT) {
            $fallback = Post::query()
                ->published()
                ->whereKeyNot($post->id)
                ->whereNotIn('id', $related->pluck('id'))
                ->with('categories')
                ->orderByDesc('published_at')
                ->orderByDesc('id')
                ->limit(self::RELATED_COUNT - $related->count())
                ->get();

            $related = $related->concat($fallback);
        }

        return $related;
    }

    /**
     * Logs every hit to `post_views` (matching the read-only PostViews
     * relation manager in Filament, which expects one row per view), but
     * only increments the denormalized `posts.view_count` counter once per
     * IP within the de-duplication window.
     */
    private function recordView(Post $post, Request $request): void
    {
        $ipAddress = (string) $request->ip();

        $alreadyViewedRecently = PostViews::query()
            ->where('post_id', $post->id)
            ->where('ip_address', $ipAddress)
            ->where('viewed_at', '>=', now()->subHours(self::VIEW_DEDUPE_WINDOW_HOURS))
            ->exists();

        PostViews::query()->create([
            'post_id' => $post->id,
            'ip_address' => $ipAddress,
            'viewed_at' => now(),
        ]);

        if (! $alreadyViewedRecently) {
            $post->increment('view_count');
        }
    }
}
