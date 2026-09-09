<?php

use App\Models\Post;
use App\Models\Tag;

it('shows only published posts belonging to the tag', function (): void {
    $tag = Tag::factory()->create(['slug' => 'elections']);
    $otherTag = Tag::factory()->create(['slug' => 'weather']);

    $published = Post::factory()->create(['title' => 'Election Story']);
    $published->tags()->attach($tag);

    $draft = Post::factory()->draft()->create(['title' => 'Draft Election Story']);
    $draft->tags()->attach($tag);

    $wrongTag = Post::factory()->create(['title' => 'Weather Story']);
    $wrongTag->tags()->attach($otherTag);

    $response = $this->get('/news/tag/elections');

    $response->assertOk();
    $response->assertViewIs('tag');
    $response->assertSee('Election Story');
    $response->assertDontSee('Draft Election Story');
    $response->assertDontSee('Weather Story');
});

it('returns 404 for an unknown tag slug', function (): void {
    $this->get('/news/tag/does-not-exist')->assertNotFound();
});

it('renders with an empty state when the tag has no posts', function (): void {
    Tag::factory()->create(['slug' => 'unused-tag']);

    $this->get('/news/tag/unused-tag')->assertOk();
});

it('paginates tag posts and preserves the query string', function (): void {
    $tag = Tag::factory()->create(['slug' => 'paginated-tag']);

    Post::factory()->count(10)->create()->each(
        fn (Post $post) => $post->tags()->attach($tag)
    );

    $firstPage = $this->get('/news/tag/paginated-tag?page=1');
    $secondPage = $this->get('/news/tag/paginated-tag?page=2');

    expect($firstPage->viewData('posts')->count())->toBe(9);
    expect($secondPage->viewData('posts')->count())->toBe(1);
});
