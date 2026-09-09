<?php

namespace App\Providers;

use App\View\Composers\SharedLayoutComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Shared layout data ($navCategories, $breakingPost, $ads, $contacts)
        // for every public page. Registered on the page views themselves
        // (not just the layout) because Blade's @extends forwards whatever
        // variables are in scope on the child view to the parent layout, so
        // composing here also makes this data available to page-specific
        // @section('content') blocks that reference it directly (e.g. the
        // category filter chips on the search page, the inline ad slots on
        // category/tag/article pages).
        View::composer(
            ['index', 'show', 'category', 'tag', 'search'],
            SharedLayoutComposer::class,
        );
    }
}
