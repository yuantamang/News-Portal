<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\PostViews;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

it('shows a published post by its slug', function (): void {
    $post = Post::factory()->create(['slug' => 'a-published-story', 'title' => 'A Published Story']);

    $response = $this->get('/news/a-published-story');

    $response->assertOk();
    $response->assertViewIs('show');
    $response->assertSee('A Published Story');
});

it('returns 404 for a post that is not published', function (string $state): void {
    Post::factory()->{$state}()->create(['slug' => 'not-public-yet']);

    $this->get('/news/not-public-yet')->assertNotFound();
})->with(['draft', 'scheduled', 'archived']);

it('returns 404 for a soft-deleted post', function (): void {
    $post = Post::factory()->create(['slug' => 'now-deleted']);
    $post->delete();

    $this->get('/news/now-deleted')->assertNotFound();
});

it('returns 404 for an unknown slug', function (): void {
    $this->get('/news/does-not-exist')->assertNotFound();
});

it('eager loads categories, tags, and media on the article page', function (): void {
    $post = Post::factory()->create(['slug' => 'with-relations']);
    $post->categories()->attach(Category::factory()->create());
    $post->tags()->attach(Tag::factory()->create());
    $post->media()->create([
        'type' => 'image',
        'file_path' => ['post/gallery.jpg'],
        'caption' => 'A caption',
    ]);

    DB::enableQueryLog();
    $this->get('/news/with-relations')->assertOk();
    $queryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    expect($queryCount)->toBeLessThan(20);
});

it('records a view and increments the view counter on first visit', function (): void {
    $post = Post::factory()->create(['slug' => 'track-me', 'view_count' => 0]);

    $this->get('/news/track-me')->assertOk();

    expect($post->fresh()->view_count)->toBe(1);
    expect(PostViews::query()->where('post_id', $post->id)->count())->toBe(1);
});

it('does not double count a view from the same IP within the dedupe window', function (): void {
    $post = Post::factory()->create(['slug' => 'track-me-twice', 'view_count' => 0]);

    $this->get('/news/track-me-twice')->assertOk();
    $this->get('/news/track-me-twice')->assertOk();

    expect($post->fresh()->view_count)->toBe(1);
    // Every hit is still logged to post_views, even when the counter doesn't move.
    expect(PostViews::query()->where('post_id', $post->id)->count())->toBe(2);
});

it('counts a view again once the dedupe window has passed', function (): void {
    $post = Post::factory()->create(['slug' => 'track-me-later', 'view_count' => 0]);

    PostViews::query()->create([
        'post_id' => $post->id,
        'ip_address' => '127.0.0.1',
        'viewed_at' => now()->subDays(2),
    ]);

    $this->get('/news/track-me-later')->assertOk();

    expect($post->fresh()->view_count)->toBe(1);
});

it('prefers related posts that share a category', function (): void {
    $sharedCategory = Category::factory()->create();

    $post = Post::factory()->create(['slug' => 'main-story']);
    $post->categories()->attach($sharedCategory);

    $related = Post::factory()->create(['title' => 'Shares Category']);
    $related->categories()->attach($sharedCategory);

    $unrelated = Post::factory()->create(['title' => 'No Shared Category']);

    $response = $this->get('/news/main-story');

    $response->assertViewHas('relatedPosts', fn ($posts) => $posts->contains(fn ($p) => $p->is($related)));
});

it('falls back to other recent posts when there are not enough same-category matches', function (): void {
    $post = Post::factory()->create(['slug' => 'lonely-story']);
    $fallback = Post::factory()->create(['title' => 'Recent Other Story']);

    $response = $this->get('/news/lonely-story');

    $response->assertViewHas('relatedPosts', fn ($posts) => $posts->contains(fn ($p) => $p->is($fallback)));
});

it('never includes the current post among its own related posts', function (): void {
    $post = Post::factory()->create(['slug' => 'self-exclude']);
    Post::factory()->count(3)->create();

    $response = $this->get('/news/self-exclude');

    $response->assertViewHas('relatedPosts', fn ($posts) => $posts->doesntContain(
        fn ($p) => $p->is($post)
    ));
});
