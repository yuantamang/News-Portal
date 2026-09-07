{{--
    Page-specific data (in addition to the layout's shared $navCategories,
    $breakingPost, $ads, $contacts):

    $post           Post   {
        title, slug, context, image, published_at, view_count, click_count,
        is_featured, is_breaking, is_trending,
        categories: Collection<Category>,   // N:N — zero, one, or many
        tags:       Collection<Tag>,        // N:N — {title, slug, context, color}
        media:      Collection<Media>,      // 1:N polymorphic, separate from $post->image
    }
    $relatedPosts   Collection<Post>   [INFERRED] — no related-post algorithm exists yet;
                     a reasonable placeholder is "other published posts sharing a category".

    There is no author on Post in the backend snapshot, so no byline/author
    box is rendered here.
--}}
@extends('layouts.app')

@section('title', $post->title)
@section('meta_description', \Illuminate\Support\Str::limit(strip_tags($post->context ?? ''), 155))

@section('content')

    <div
        id="reading-progress"
        class="fixed top-0 left-0 h-[3px] w-full bg-primary z-[60] reading-progress"
        style="--progress: 0"
        aria-hidden="true"
    ></div>

    {{-- Breadcrumb --}}
    <nav aria-label="Breadcrumb" class="border-b border-black/10 py-3">
        <ol class="mx-auto max-w-7xl px-4 flex items-center gap-2 font-sans text-xs uppercase tracking-wide text-black/55">
            <li><a href="/" class="hover:text-black">Home</a></li>
            @if ($post->categories->isNotEmpty())
                <li aria-hidden="true">/</li>
                <li><a href="/news/category/{{ $post->categories->first()->slug }}" class="hover:text-black">{{ $post->categories->first()->type }}</a></li>
            @endif
        </ol>
    </nav>

    {{-- Header --}}
    <header class="mx-auto max-w-measure px-4 pt-10">
        <div class="flex items-center gap-3 flex-wrap">
            @if ($post->is_breaking)
                <span class="font-sans text-[10px] font-bold uppercase tracking-wide bg-primary text-primary-content px-1.5 py-0.5">Breaking</span>
            @endif
            @if ($post->is_trending)
                <span class="font-sans text-[10px] font-bold uppercase tracking-wide border border-black/20 text-black/60 px-1.5 py-0.5">Trending</span>
            @endif
            @foreach ($post->categories as $category)
                <x-category-badge :category="$category" />
            @endforeach
        </div>

        <h1 class="font-serif text-3xl sm:text-4xl md:text-5xl leading-[1.05] tracking-tight mt-3">
            {{ $post->title }}
        </h1>

        <div class="flex flex-wrap items-center gap-x-4 gap-y-2 border-y border-black/10 py-4 mt-6">
            <div class="font-sans text-xs text-black/60 flex flex-wrap items-center gap-x-4 gap-y-1">
                @if ($post->published_at)
                    <time datetime="{{ $post->published_at->toIso8601String() }}">
                        {{ $post->published_at->format('M j, Y') }}
                    </time>
                @endif
                <span class="border-l border-black/15 pl-4">{{ number_format($post->view_count) }} views</span>
            </div>

            <div class="flex items-center gap-2 ml-auto">
                <button
                    type="button"
                    id="copy-link-btn"
                    class="btn btn-ghost btn-sm rounded-none border border-black/15 gap-1.5"
                    data-url="/news/{{ $post->slug }}"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M10 13a5 5 0 0 0 7.07 0l2.83-2.83a5 5 0 0 0-7.07-7.07L11 4.93" stroke-linecap="round" />
                        <path d="M14 11a5 5 0 0 0-7.07 0L4.1 13.83a5 5 0 0 0 7.07 7.07L13 19.07" stroke-linecap="round" />
                    </svg>
                    <span data-label>Copy Link</span>
                </button>
                <a href="mailto:?subject={{ urlencode($post->title) }}" class="btn btn-ghost btn-sm rounded-none border border-black/15 gap-1.5" aria-label="Share via email">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <rect x="3" y="5" width="18" height="14" />
                        <path d="m3 7 9 6 9-6" />
                    </svg>
                    Email
                </a>
            </div>
        </div>
    </header>

    {{-- Lead image (Post's own `image` field — not a Media record) --}}
    @if ($post->image)
        <figure class="mx-auto max-w-5xl px-4 mt-8">
            <img
                src="{{ $post->image }}"
                alt="{{ $post->title }}"
                class="w-full aspect-[16/9] object-cover border border-black/10"
            >
        </figure>
    @endif

    {{-- Body — reuses the header's exact `mx-auto max-w-measure px-4` box so the
         reading column lines up perfectly. The rail sits in an absolutely
         positioned margin column (horizontal placement only); the sticky
         behaviour lives on the inner element, so the two concerns never
         fight over the same `position` value. --}}
    <div class="mx-auto max-w-measure px-4 mt-10 relative">
        <div class="hidden lg:block absolute right-full mr-6 top-0" aria-hidden="true">
            <div class="sticky top-28 flex flex-col gap-2">
                <button type="button" class="btn btn-ghost btn-square btn-sm rounded-none border border-black/15" data-share-scroll aria-label="Copy link">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.07 0l2.83-2.83a5 5 0 0 0-7.07-7.07L11 4.93" stroke-linecap="round" />
                        <path d="M14 11a5 5 0 0 0-7.07 0L4.1 13.83a5 5 0 0 0 7.07 7.07L13 19.07" stroke-linecap="round" />
                    </svg>
                </button>
            </div>
        </div>

        <div class="article-body font-serif text-lg leading-[1.75] text-base-content" data-article-body>
            {!! $post->context !!}
        </div>

        @if (($ads['inline'] ?? collect())->isNotEmpty())
            <div class="my-10">
                <x-ad-slot :ads="$ads['inline']" position="inline" />
            </div>
        @endif

        {{-- Post's polymorphic media, separate from the single `image` field above --}}
        <x-media-gallery :media="$post->media" class="mt-10" />
    </div>

    {{-- Tags --}}
    @if ($post->tags->isNotEmpty())
        <div class="mx-auto max-w-measure px-4 mt-12 pt-6 border-t border-black/10">
            <h2 class="sr-only">Tags</h2>
            <ul class="flex flex-wrap gap-2">
                @foreach ($post->tags as $tag)
                    <li><x-tag-badge :tag="$tag" /></li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Related coverage — no related-post algorithm exists in the backend yet;
         controller can start with "other published posts sharing a category" --}}
    <div class="mx-auto max-w-5xl px-4 mt-14 pt-8 border-t border-black/10">
        <h2 class="font-sans text-sm font-bold uppercase tracking-wide border-b border-black pb-2 mb-6">
            @if ($post->categories->isNotEmpty())
                More in {{ $post->categories->first()->type }}
            @else
                Related Coverage
            @endif
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 divide-y md:divide-y-0 md:divide-x divide-black/10">
            @forelse ($relatedPosts ?? [] as $related)
                <x-post-card
                    :post="$related"
                    size="sm"
                    class="pt-8 first:pt-0 md:pt-0 md:px-8 md:first:pl-0 md:last:pr-0"
                />
            @empty
                <p class="font-serif text-sm text-black/50">Related coverage will appear here.</p>
            @endforelse
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        (function () {
            var bar = document.getElementById('reading-progress');
            var body = document.querySelector('[data-article-body]');

            function updateProgress() {
                if (!bar || !body) return;
                var rect = body.getBoundingClientRect();
                var total = body.offsetHeight - window.innerHeight;
                var scrolled = Math.min(Math.max(-rect.top, 0), Math.max(total, 1));
                var pct = total > 0 ? scrolled / total : 0;
                bar.style.setProperty('--progress', pct.toFixed(4));
            }

            window.addEventListener('scroll', updateProgress, { passive: true });
            window.addEventListener('resize', updateProgress);
            updateProgress();

            function copyLink() {
                var path = document.getElementById('copy-link-btn').getAttribute('data-url');
                var url = window.location.origin + path;
                navigator.clipboard.writeText(url).then(function () {
                    var label = document.querySelector('#copy-link-btn [data-label]');
                    if (!label) return;
                    var original = label.textContent;
                    label.textContent = 'Copied';
                    setTimeout(function () { label.textContent = original; }, 2000);
                }).catch(function (err) {
                    console.error('Copy failed', err);
                });
            }

            var copyBtn = document.getElementById('copy-link-btn');
            if (copyBtn) copyBtn.addEventListener('click', copyLink);

            var railBtn = document.querySelector('[data-share-scroll]');
            if (railBtn) railBtn.addEventListener('click', copyLink);
        })();
    </script>
@endpush
