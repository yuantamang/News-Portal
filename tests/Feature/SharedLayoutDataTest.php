<?php

use App\Enums\AdvertisementPosition;
use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Post;

it('lists real categories in the navigation on every public page', function (): void {
    Category::factory()->create(['type' => 'World News']);
    Category::factory()->create(['type' => 'Local Sports']);

    $this->get(route('home'))
        ->assertSee('World News')
        ->assertSee('Local Sports');
});

it('shows the breaking news banner for the most recent breaking post', function (): void {
    Post::factory()->breaking()->create([
        'title' => 'Older Breaking Story',
        'published_at' => now()->subDays(3),
    ]);
    $newest = Post::factory()->breaking()->create([
        'title' => 'Newest Breaking Story',
        'published_at' => now()->subDay(),
    ]);

    $response = $this->get(route('home'));

    expect($response->viewData('breakingPost')->is($newest))->toBeTrue();
});

it('has no breaking post banner data when nothing is flagged as breaking', function (): void {
    Post::factory()->create();

    $response = $this->get(route('home'));

    expect($response->viewData('breakingPost'))->toBeNull();
});

it('only shows ads that are active and within their date window, grouped by position', function (): void {
    $activeHeaderAd = Advertisement::factory()->position(AdvertisementPosition::HEADER)->create([
        'title' => 'Active Header Ad',
    ]);
    Advertisement::factory()->inactive()->position(AdvertisementPosition::HEADER)->create([
        'title' => 'Inactive Header Ad',
    ]);
    Advertisement::factory()->expired()->position(AdvertisementPosition::HEADER)->create([
        'title' => 'Expired Header Ad',
    ]);
    Advertisement::factory()->upcoming()->position(AdvertisementPosition::HEADER)->create([
        'title' => 'Upcoming Header Ad',
    ]);

    $response = $this->get(route('home'));

    $response->assertSee('Active Header Ad');
    $response->assertDontSee('Inactive Header Ad');
    $response->assertDontSee('Expired Header Ad');
    $response->assertDontSee('Upcoming Header Ad');
    expect($response->viewData('ads')->get('header')->pluck('title'))->toContain('Active Header Ad');
});

it('keeps every ad position present in shared data even when there are no ads at all', function (): void {
    $response = $this->get(route('home'));

    $ads = $response->viewData('ads');

    foreach (AdvertisementPosition::cases() as $position) {
        expect($ads->has($position->value))->toBeTrue();
        expect($ads->get($position->value))->toBeEmpty();
    }
});

it('does not mix ads from different positions', function (): void {
    Advertisement::factory()->position(AdvertisementPosition::SIDBAR)->create(['title' => 'Sidebar Only Ad']);
    Advertisement::factory()->position(AdvertisementPosition::HEADER)->create(['title' => 'Header Only Ad']);

    $response = $this->get(route('home'));
    $ads = $response->viewData('ads');

    expect($ads->get('sidebar')->pluck('title'))->toContain('Sidebar Only Ad');
    expect($ads->get('sidebar')->pluck('title'))->not->toContain('Header Only Ad');
    expect($ads->get('header')->pluck('title'))->toContain('Header Only Ad');
});

it('shows contact details in the footer', function (): void {
    Contact::factory()->create([
        'phone_number' => '555-010-2020',
        'email' => 'newsroom@example.test',
    ]);

    $this->get(route('home'))
        ->assertSee('555-010-2020')
        ->assertSee('newsroom@example.test');
});
