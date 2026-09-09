{{--
    Page-specific data (in addition to the layout's shared $navCategories,
    $breakingPost, $ads, $contacts):

    $featuredPost   Post|null        most recently published post where is_featured = true (the hero)
    $latestPosts    Collection<Post> published posts ordered by published_at desc, excluding $featuredPost
    $morePosts      Collection<Post> further published posts for the secondary grid
    $trendingPosts  Collection<Post> posts where is_trending = true
    $popularPosts   Collection<Post> published posts ordered by view_count desc

    Every collection below renders an empty-state message via @forelse when
    empty, so this view is safe to render before any of this is wired up.
--}}
@extends('layouts.app')

@section('title', 'Home')
@section('meta_description', 'Top stories from The Broadsheet — breaking news, featured coverage, and analysis.')

@section('content')

    {{-- Featured story + latest rail --}}
    <section class="border-b border-black/10 py-8 md:py-10">
        <div class="mx-auto max-w-7xl px-4 grid grid-cols-1 lg:grid-cols-12 gap-8">

            <article class="lg:col-span-8 lg:border-r lg:border-black/10 lg:pr-8">
                @if ($featuredPost)
                    <div class="flex items-center gap-2 flex-wrap">
                        @if ($featuredPost->is_breaking)
                            <span class="font-sans text-[10px] font-bold uppercase tracking-wide bg-primary text-primary-content px-1.5 py-0.5">Breaking</span>
                        @endif
                        <x-category-badge :category="$featuredPost->categories->first() ?? null" />
                    </div>

                    <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl leading-[1.05] tracking-tight mt-3">
                        <a href="/news/{{ $featuredPost->slug }}" class="hover:underline decoration-black/20 underline-offset-4">
                            {{ $featuredPost->title }}
                        </a>
                    </h1>

                    <p class="font-serif text-lg md:text-xl text-black/70 leading-snug mt-4 max-w-2xl">
                        {{ \Illuminate\Support\Str::limit(strip_tags($featuredPost->context), 180) }}
                    </p>

                    @if ($featuredPost->image)
                        <a href="/news/{{ $featuredPost->slug }}" class="block border border-black/10 overflow-hidden mt-6">
                            <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($featuredPost->image) }}" alt="" class="w-full aspect-[16/9] object-cover">
                        </a>
                    @endif

                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 font-sans text-xs text-black/55 mt-4">
                        @if ($featuredPost->published_at)
                            <time datetime="{{ $featuredPost->published_at->toIso8601String() }}">{{ $featuredPost->published_at->format('M j, Y') }}</time>
                        @endif
                        <span class="border-l border-black/15 pl-4">{{ number_format($featuredPost->view_count) }} views</span>
                    </div>
                @else
                    <p class="font-serif text-black/50">No featured story is set yet.</p>
                @endif
            </article>

            <aside class="lg:col-span-4 lg:pl-8 flex flex-col gap-8" aria-labelledby="latest-heading">
                <div>
                    <h2 id="latest-heading" class="font-sans text-sm font-bold uppercase tracking-wide border-b border-black pb-2 mb-1">
                        Latest
                    </h2>
                    @forelse ($latestPosts as $post)
                        <article class="border-t border-black/10 py-4 first:border-t-0">
                            <x-category-badge :category="$post->categories->first() ?? null" />
                            <h3 class="font-serif text-base leading-snug mt-1">
                                <a href="/news/{{ $post->slug }}" class="hover:underline decoration-black/20 underline-offset-4">
                                    {{ $post->title }}
                                </a>
                            </h3>
                            @if ($post->published_at)
                                <time class="font-sans text-[11px] text-black/45 uppercase tracking-wide mt-1.5 block">
                                    {{ $post->published_at->diffForHumans() }}
                                </time>
                            @endif
                        </article>
                    @empty
                        <p class="font-serif text-sm text-black/50 py-4">More stories are on the way.</p>
                    @endforelse
                </div>

                <x-ad-slot :ads="$ads['sidebar'] ?? collect()" position="sidebar" />
            </aside>

        </div>
    </section>

    {{-- More stories --}}
    <section class="border-b border-black/10 py-8 md:py-10">
        <div class="mx-auto max-w-7xl px-4">
            <h2 class="sr-only">More Stories</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 divide-y sm:divide-y-0 lg:divide-x divide-black/10">
                @forelse ($morePosts as $post)
                    <x-post-card
                        :post="$post"
                        size="sm"
                        class="pt-8 first:pt-0 sm:pt-0 lg:pl-8 lg:first:pl-0"
                    />
                @empty
                    <p class="font-serif text-sm text-black/50">More stories are on the way.</p>
                @endforelse
            </div>
        </div>
    </section>

    <div class="mx-auto max-w-7xl px-4 py-8">
        <x-ad-slot :ads="$ads['inline'] ?? collect()" position="inline" />
    </div>

    {{-- Trending (is_trending = true) --}}
    <section class="border-b border-black/10 py-8 md:py-10">
        <div class="mx-auto max-w-7xl px-4">
            <h2 class="font-sans text-sm font-bold uppercase tracking-wide border-b border-black pb-2 mb-6">
                Trending Now
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8 divide-y sm:divide-y-0 lg:divide-x divide-black/10">
                @forelse ($trendingPosts as $post)
                    <x-post-card
                        :post="$post"
                        size="sm"
                        class="pt-8 first:pt-0 sm:pt-0 lg:pl-8 lg:first:pl-0"
                    />
                @empty
                    <p class="font-serif text-sm text-black/50">Nothing is trending right now.</p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Most read (ranked by view_count) --}}
    <section class="py-8 md:py-10">
        <div class="mx-auto max-w-7xl px-4">
            <h2 class="font-sans text-sm font-bold uppercase tracking-wide border-b border-black pb-2 mb-1">
                Most Read
            </h2>
            <ol class="max-w-2xl">
                @forelse ($popularPosts as $post)
                    <li class="flex items-baseline gap-4 border-t border-black/10 py-3 first:border-t-0">
                        <span class="font-serif text-3xl md:text-4xl text-black/20 leading-none w-10 flex-shrink-0" aria-hidden="true">
                            {{ $loop->iteration }}
                        </span>
                        <a href="/news/{{ $post->slug }}" class="font-serif text-lg leading-snug hover:underline decoration-black/20 underline-offset-4">
                            {{ $post->title }}
                        </a>
                        <span class="ml-auto font-sans text-[11px] text-black/40 uppercase tracking-wide flex-shrink-0">
                            {{ number_format($post->view_count) }} views
                        </span>
                    </li>
                @empty
                    <p class="font-serif text-sm text-black/50 py-4">No view data yet.</p>
                @endforelse
            </ol>
        </div>
    </section>

@endsection
