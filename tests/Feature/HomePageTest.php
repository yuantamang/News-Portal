<?php

use App\Models\Category;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

it('renders the homepage successfully with no posts at all', function (): void {
    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertViewIs('index');
    expect($response->viewData('featuredPost'))->toBeNull();
    expect($response->viewData('latestPosts'))->toBeEmpty();
    expect($response->viewData('morePosts'))->toBeEmpty();
    expect($response->viewData('trendingPosts'))->toBeEmpty();
    expect($response->viewData('popularPosts'))->toBeEmpty();
});

it('only shows published posts on the homepage', function (): void {
    $published = Post::factory()->create(['title' => 'Published Story']);
    Post::factory()->draft()->create(['title' => 'Draft Story']);
    Post::factory()->scheduled()->create(['title' => 'Scheduled Story']);
    Post::factory()->archived()->create(['title' => 'Archived Story']);

    $response = $this->get(route('home'));

    $response->assertOk();
    $response->assertSee('Published Story');
    $response->assertDontSee('Draft Story');
    $response->assertDontSee('Scheduled Story');
    $response->assertDontSee('Archived Story');
});

it('excludes soft-deleted posts from the homepage', function (): void {
    $post = Post::factory()->create(['title' => 'Deleted Story']);
    $post->delete();

    $this->get(route('home'))->assertDontSee('Deleted Story');
});

it('picks the newest featured post and excludes it from the latest rail', function (): void {
    $older = Post::factory()->featured()->create([
        'title' => 'Older Featured',
        'published_at' => now()->subDays(5),
    ]);
    $newer = Post::factory()->featured()->create([
        'title' => 'Newer Featured',
        'published_at' => now()->subDay(),
    ]);

    $response = $this->get(route('home'));

    $response->assertViewHas('featuredPost', fn ($post) => $post->is($newer));
    $response->assertViewHas('latestPosts', fn ($posts) => $posts->doesntContain(
        fn ($post) => $post->is($newer)
    ));
});

it('only includes is_trending posts in the trending rail', function (): void {
    $trending = Post::factory()->trending()->create(['title' => 'Trending Story']);
    Post::factory()->create(['title' => 'Non Trending Story']);

    $response = $this->get(route('home'));

    $response->assertViewHas('trendingPosts', fn ($posts) => $posts->count() === 1
        && $posts->first()->is($trending));
});

it('orders the most read rail by view_count descending', function (): void {
    $lowViews = Post::factory()->create(['view_count' => 10]);
    $highViews = Post::factory()->create(['view_count' => 500]);

    $response = $this->get(route('home'));

    $response->assertViewHas('popularPosts', fn ($posts) => $posts->first()->is($highViews)
        && $posts->last()->is($lowViews));
});

it('eager loads categories for every homepage rail to avoid N+1 queries', function (): void {
    $category = Category::factory()->create();
    $post = Post::factory()->featured()->create();
    $post->categories()->attach($category);

    Post::factory()->count(5)->create();

    DB::enableQueryLog();
    $this->get(route('home'))->assertOk();
    $queryCount = count(DB::getQueryLog());
    DB::disableQueryLog();

    // A handful of fixed queries (5 post rails + shared layout data), not one
    // extra query per post for its categories.
    expect($queryCount)->toBeLessThan(20);
});
