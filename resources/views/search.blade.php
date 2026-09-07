{{--
    Page-specific data (in addition to the layout's shared $navCategories,
    $breakingPost, $ads, $contacts):

    $query   string|null                  raw ?q= term; null/empty before a search is submitted
    $posts   LengthAwarePaginator<Post>|null   search results — the search implementation
             itself doesn't exist yet ([UNKNOWN] per the backend docs), so this
             renders an empty state until it does
--}}
@extends('layouts.app')

@section('title', $query ? 'Search: ' . $query : 'Search')
@section('meta_description', 'Search articles on The Broadsheet.')

@section('content')

    <div class="mx-auto max-w-7xl px-4 py-8 border-b border-black/10">
        <p class="font-sans text-xs font-bold uppercase tracking-wide text-primary">Search</p>
        <h1 class="font-serif text-3xl md:text-4xl tracking-tight mt-1">
            {{ $query ? 'Results for “' . $query . '”' : 'Search articles' }}
        </h1>

        <x-search-input id="search-page-input" :query="$query" class="max-w-lg mt-6" />

        @if (($navCategories ?? collect())->isNotEmpty())
            <div class="flex flex-wrap items-center gap-2 mt-4">
                <span class="font-sans text-[11px] uppercase tracking-wide text-black/45">Filter:</span>
                @foreach ($navCategories as $category)
                    <a
                        href="/search?q={{ urlencode((string) $query) }}&amp;category={{ $category->slug }}"
                        class="badge badge-outline rounded-none border-black/25 text-black/65 font-sans text-[11px] uppercase tracking-wide px-3 py-3 hover:border-black hover:text-black"
                    >
                        {{ $category->type }}
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    <section class="mx-auto max-w-7xl px-4 py-8">
        @if (! $query)
            <p class="font-serif text-black/50">Enter a search term above to find articles.</p>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 divide-y sm:divide-y-0 lg:divide-x divide-black/10">
                @forelse ($posts ?? [] as $post)
                    <x-post-card
                        :post="$post"
                        class="pt-8 first:pt-0 sm:pt-0 lg:pl-8 lg:first:pl-0"
                    />
                @empty
                    <p class="font-serif text-black/50">No articles matched “{{ $query }}”.</p>
                @endforelse
            </div>

            @if (isset($posts) && method_exists($posts, 'links'))
                <div class="mt-10">
                    {{ $posts->links() }}
                </div>
            @endif
        @endif
    </section>

@endsection
