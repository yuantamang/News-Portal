<?php

use App\Models\Category;
use App\Models\Post;

it('does not run a search when no query is given', function (): void {
    Post::factory()->create(['title' => 'Some Story']);

    $response = $this->get('/search');

    $response->assertOk();
    $response->assertViewIs('search');
    expect($response->viewData('query'))->toBeNull();
    expect($response->viewData('posts'))->toBeNull();
});

it('matches posts by title', function (): void {
    Post::factory()->create(['title' => 'Historic Election Results Announced']);
    Post::factory()->create(['title' => 'Local Weather Update']);

    $response = $this->get('/search?q=election');

    $response->assertOk();
    expect($response->viewData('query'))->toBe('election');
    $response->assertSee('Historic Election Results Announced');
    $response->assertDontSee('Local Weather Update');
});

it('matches posts by context content', function (): void {
    Post::factory()->create([
        'title' => 'Completely Unrelated Headline',
        'context' => '<p>This article covers the volcanic eruption in detail.</p>',
    ]);
    Post::factory()->create([
        'title' => 'Another Headline',
        'context' => '<p>This one is about something else entirely.</p>',
    ]);

    $response = $this->get('/search?q=volcanic');

    $response->assertSee('Completely Unrelated Headline');
    $response->assertDontSee('Another Headline');
});

it('only searches published posts', function (): void {
    Post::factory()->draft()->create(['title' => 'Draft Election Coverage']);

    $response = $this->get('/search?q=election');

    $response->assertDontSee('Draft Election Coverage');
});

it('combines the text query with a category filter', function (): void {
    $category = Category::factory()->create(['slug' => 'politics']);
    $otherCategory = Category::factory()->create(['slug' => 'sports']);

    $inCategory = Post::factory()->create(['title' => 'Election Night in Politics']);
    $inCategory->categories()->attach($category);

    $outOfCategory = Post::factory()->create(['title' => 'Election Night in Sports Town']);
    $outOfCategory->categories()->attach($otherCategory);

    $response = $this->get('/search?q=election&category=politics');

    $response->assertSee('Election Night in Politics');
    $response->assertDontSee('Election Night in Sports Town');
});

it('renders an empty paginator when the search has no matches', function (): void {
    Post::factory()->create(['title' => 'Nothing Related']);

    $response = $this->get('/search?q=zzznomatchzzz');

    $response->assertOk();
    expect($response->viewData('posts')->total())->toBe(0);
});

it('preserves q and category across pagination links', function (): void {
    $category = Category::factory()->create(['slug' => 'breaking']);

    Post::factory()->count(11)->create(['title' => 'Breaking Update Story'])->each(
        fn (Post $post) => $post->categories()->attach($category)
    );

    $response = $this->get('/search?q=breaking&category=breaking&page=1');

    $response->assertOk();
    $paginator = $response->viewData('posts');

    expect($paginator->count())->toBe(9);
    expect(str_contains((string) $paginator->nextPageUrl(), 'q=breaking'))->toBeTrue();
    expect(str_contains((string) $paginator->nextPageUrl(), 'category=breaking'))->toBeTrue();
});
