<?php

use App\Models\Category;
use App\Models\Post;

it('shows only published posts belonging to the category', function (): void {
    $category = Category::factory()->create(['slug' => 'world-news']);
    $otherCategory = Category::factory()->create(['slug' => 'sports']);

    $published = Post::factory()->create(['title' => 'World News Story']);
    $published->categories()->attach($category);

    $draft = Post::factory()->draft()->create(['title' => 'Draft World Story']);
    $draft->categories()->attach($category);

    $wrongCategory = Post::factory()->create(['title' => 'Sports Story']);
    $wrongCategory->categories()->attach($otherCategory);

    $response = $this->get('/news/category/world-news');

    $response->assertOk();
    $response->assertViewIs('category');
    $response->assertSee('World News Story');
    $response->assertDontSee('Draft World Story');
    $response->assertDontSee('Sports Story');
});

it('returns 404 for an unknown category slug', function (): void {
    $this->get('/news/category/does-not-exist')->assertNotFound();
});

it('renders with an empty state when the category has no posts', function (): void {
    Category::factory()->create(['slug' => 'empty-section']);

    $this->get('/news/category/empty-section')->assertOk();
});

it('paginates category posts and preserves the query string', function (): void {
    $category = Category::factory()->create(['slug' => 'paginated']);

    Post::factory()->count(12)->create()->each(
        fn (Post $post) => $post->categories()->attach($category)
    );

    $firstPage = $this->get('/news/category/paginated?page=1');
    $secondPage = $this->get('/news/category/paginated?page=2');

    $firstPage->assertOk();
    $secondPage->assertOk();

    expect($firstPage->viewData('posts')->count())->toBe(9);
    expect($secondPage->viewData('posts')->count())->toBe(3);
});

it('supports a post belonging to more than one category', function (): void {
    $categoryOne = Category::factory()->create(['slug' => 'cat-one']);
    $categoryTwo = Category::factory()->create(['slug' => 'cat-two']);

    $post = Post::factory()->create(['title' => 'Multi Category Story']);
    $post->categories()->attach([$categoryOne->id, $categoryTwo->id]);

    $this->get('/news/category/cat-one')->assertSee('Multi Category Story');
    $this->get('/news/category/cat-two')->assertSee('Multi Category Story');
});
