{{--
    Page-specific data (in addition to the layout's shared $navCategories,
    $breakingPost, $ads, $contacts):

    $featuredPost   Post|null        most recently published post where is_featured = true
    $latestPosts    Collection<Post> published posts ordered by published_at desc
    $morePosts      Collection<Post> further published posts for the secondary grid
    $trendingPosts  Collection<Post> posts where is_trending = true
    $popularPosts   Collection<Post> published posts ordered by view_count desc

    Every collection below renders nothing (not an empty-state filler section)
    when it's empty, so this view stays intentional-looking whether there are
    2 posts or 200 — see the per-section @if/@empty checks.
--}}
@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', 'Top stories from The Broadsheet — breaking news, featured coverage, and analysis.')

@section('content')

    @php
        // If nothing is marked featured, the most recent post stands in as the
        // hero rather than showing an empty "no featured story" message —
        // there's no reason to waste the most prominent slot on the homepage
        // just because is_featured hasn't been set on anything yet.
        $heroPost = $featuredPost ?? $latestPosts->first();

        // Whatever ends up as the hero is excluded from the rail beside it,
        // so the same story never appears twice in the same glance. Capped
        // at 3 (not more) so the sidebar stays roughly in proportion to the
        // hero rather than running well past it.
        $secondaryPosts = $heroPost
            ? $latestPosts->reject(fn ($p) => $p->id === $heroPost->id)->take(3)
            : collect();

        $sidebarAds = $ads['sidebar'] ?? collect();
        $hasSidebar = $secondaryPosts->isNotEmpty() || $popularPosts->isNotEmpty() || $sidebarAds->isNotEmpty();

        $trendingCount = $trendingPosts->count();
        $trendingCols = match (true) {
            $trendingCount >= 4 => 'sm:grid-cols-2 lg:grid-cols-4',
            $trendingCount === 3 => 'sm:grid-cols-3',
            $trendingCount === 2 => 'sm:grid-cols-2',
            default => 'grid-cols-1',
        };
        // With only 1-2 trending posts, a full-bleed grid column would
        // stretch each card far wider than its content warrants.
        $trendingCardCap = $trendingCount <= 2 ? 'max-w-md mx-auto w-full' : '';

        $mostReadCols = $popularPosts->count() >= 5 ? 'lg:columns-2' : 'lg:columns-1';
    @endphp

    {{-- Hero + sidebar --}}
    @if ($heroPost)
        <section class="border-b border-black/10 py-6 md:py-8">
            {{-- items-start: the hero and sidebar are different kinds of
                 content (one long-form feature vs. a stack of widgets), so
                 they should never stretch to match each other's height —
                 that's what let the sidebar ad dictate the hero's box size. --}}
            <div class="mx-auto max-w-7xl px-4 grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

                <article class="{{ $hasSidebar ? 'lg:col-span-7 lg:border-r lg:border-black/10 lg:pr-8' : 'lg:col-span-12' }}">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($heroPost->is_breaking)
                            <span class="font-sans text-[10px] font-bold uppercase tracking-wide bg-primary text-primary-content px-1.5 py-0.5">Breaking</span>
                        @endif
                        <x-category-badge :category="$heroPost->categories->first() ?? null" />
                    </div>

                    <h1 class="font-serif font-black text-4xl sm:text-5xl {{ $hasSidebar ? 'md:text-6xl' : 'md:text-7xl' }} leading-[0.95] tracking-tight mt-3">
                        <a href="{{ route('news.show', $heroPost->slug) }}" class="hover:underline decoration-black/20 underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                            {{ $heroPost->title }}
                        </a>
                    </h1>

                    <p class="font-serif text-lg md:text-xl text-black/70 leading-relaxed mt-4 max-w-2xl">
                        {{ \Illuminate\Support\Str::limit(strip_tags($heroPost->context), 180) }}
                    </p>

                    @if ($heroPost->image)
                        <a href="{{ route('news.show', $heroPost->slug) }}" class="block border border-black/10 overflow-hidden mt-6 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-primary">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($heroPost->image) }}" alt="" class="w-full aspect-[3/2] object-cover">
                        </a>
                    @endif

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 font-sans text-xs text-black/55 mt-6 pt-4 border-t border-black/10">
                        @if ($heroPost->published_at)
                            <time datetime="{{ $heroPost->published_at->toIso8601String() }}">{{ $heroPost->published_at->format('M j, Y') }}</time>
                        @endif
                        <span class="border-l border-black/15 pl-4">{{ number_format($heroPost->view_count) }} views</span>
                    </div>
                </article>

                @if ($hasSidebar)
                    <aside class="lg:col-span-5 lg:pl-8">

                        @if ($secondaryPosts->isNotEmpty())
                            <div>
                                <h2 class="font-sans text-sm font-bold uppercase tracking-wide border-b border-black pb-2 mb-4">
                                    Latest
                                </h2>
                                <div class="flex flex-col gap-3">
                                    @foreach ($secondaryPosts as $post)
                                        <x-post-card
                                            :post="$post"
                                            layout="horizontal"
                                            size="sm"
                                            :show-excerpt="false"
                                            class="p-2 -mx-2 hover:bg-base-200 transition-colors"
                                        />
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if ($popularPosts->isNotEmpty())
                            <div class="{{ $secondaryPosts->isNotEmpty() ? 'mt-6 pt-5 border-t-2 border-black' : '' }}">
                                <h2 class="font-sans text-sm font-bold uppercase tracking-wide {{ $secondaryPosts->isEmpty() ? 'border-b border-black pb-2' : '' }} mb-4">
                                    Most Read
                                </h2>
                                <ol class="flex flex-col gap-2">
                                    @foreach ($popularPosts->take(3) as $post)
                                        <li class="flex items-baseline gap-3">
                                            <span class="font-serif text-xl text-black/25 leading-none flex-shrink-0" aria-hidden="true">{{ $loop->iteration }}</span>
                                            <a href="{{ route('news.show', $post->slug) }}" class="font-serif text-sm leading-snug hover:underline decoration-black/20 underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                                                {{ $post->title }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @endif

                        @if ($sidebarAds->isNotEmpty())
                            <div class="{{ $secondaryPosts->isNotEmpty() || $popularPosts->isNotEmpty() ? 'mt-6' : '' }}">
                                <x-ad-slot :ads="$sidebarAds" position="sidebar" />
                            </div>
                        @endif

                    </aside>
                @endif

            </div>
        </section>
    @endif

    {{-- More headlines --}}
    @if ($morePosts->isNotEmpty())
        <section class="border-b border-black/10 py-6 md:py-8">
            <div class="mx-auto max-w-7xl px-4">
                <h2 class="font-sans text-sm font-bold uppercase tracking-wide border-b border-black pb-2 mb-6">
                    More Headlines
                </h2>

                @if ($morePosts->count() <= 2)
                    {{-- Too few for the asymmetric split below to read as intentional —
                         a simple, generously-sized grid instead. --}}
                    <div class="grid grid-cols-1 {{ $morePosts->count() === 2 ? 'sm:grid-cols-2' : '' }} gap-6">
                        @foreach ($morePosts as $post)
                            <x-post-card :post="$post" size="md" class="border border-black/10 hover:border-black/25 transition-colors p-5 max-w-xl mx-auto w-full" />
                        @endforeach
                    </div>
                @else
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                        <div class="lg:col-span-5 lg:border-r lg:border-black/10 lg:pr-8">
                            <x-post-card :post="$morePosts->first()" size="lg" />
                        </div>

                        <div class="lg:col-span-7 lg:pl-8 flex flex-col gap-3">
                            @foreach ($morePosts->skip(1) as $post)
                                <x-post-card
                                    :post="$post"
                                    size="sm"
                                    :show-image="false"
                                    :show-excerpt="false"
                                    class="border border-black/10 hover:border-black/25 transition-colors p-3.5"
                                />
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    @endif

    @if (($ads['inline'] ?? collect())->isNotEmpty())
        <div class="mx-auto max-w-7xl px-4 py-8">
            <x-ad-slot :ads="$ads['inline']" position="inline" />
        </div>
    @endif

    {{-- Trending (is_trending = true) — set off with a quiet band so it reads
         as its own desk rather than a continuation of the grid above it. --}}
    @if ($trendingPosts->isNotEmpty())
        <section class="bg-base-200 border-b border-black/10 py-6 md:py-8">
            <div class="mx-auto max-w-7xl px-4">
                <h2 class="font-sans text-sm font-bold uppercase tracking-wide border-b border-black pb-2 mb-6">
                    Trending Now
                </h2>
                <div class="grid {{ $trendingCols }} gap-6">
                    @foreach ($trendingPosts as $post)
                        <x-post-card
                            :post="$post"
                            size="sm"
                            class="bg-base-100 border border-black/10 hover:border-black/25 transition-colors p-4 {{ $trendingCardCap }}"
                        />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Most read (ranked by view_count) --}}
    @if ($popularPosts->isNotEmpty())
        <section class="py-6 md:py-8">
            <div class="mx-auto max-w-7xl px-4">
                <h2 class="font-sans text-sm font-bold uppercase tracking-wide border-b border-black pb-1 mb-1">
                    Most Read
                </h2>
                <ol class="{{ $mostReadCols }} gap-x-16">
                    @foreach ($popularPosts as $post)
                        <li class="flex items-baseline gap-4 border-t border-black/10 py-3 px-2 -mx-2 hover:bg-base-200 transition-colors break-inside-avoid">
                            <span class="font-serif text-3xl md:text-4xl text-black/20 leading-none w-10 flex-shrink-0" aria-hidden="true">
                                {{ $loop->iteration }}
                            </span>
                            <a href="{{ route('news.show', $post->slug) }}" class="font-serif text-lg leading-snug hover:underline decoration-black/20 underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                                {{ $post->title }}
                            </a>
                            <span class="ml-auto font-sans text-[11px] text-black/40 uppercase tracking-wide flex-shrink-0">
                                {{ number_format($post->view_count) }} views
                            </span>
                        </li>
                    @endforeach
                </ol>
            </div>
        </section>
    @endif

    @if (! $heroPost && $morePosts->isEmpty() && $trendingPosts->isEmpty() && $popularPosts->isEmpty())
        <section class="py-24 text-center">
            <p class="font-serif text-black/50">No stories have been published yet.</p>
        </section>
    @endif

@endsection
