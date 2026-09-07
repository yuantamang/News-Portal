{{--
    Page-specific data (in addition to the layout's shared $navCategories,
    $breakingPost, $ads, $contacts):

    $category   Category                {type, slug} — Category has no name/description field
    $posts      LengthAwarePaginator<Post>   published posts belonging to this category
--}}
@extends('layouts.app')

@section('title', $category->type)
@section('meta_description', 'Coverage filed under ' . $category->type . ' from The Broadsheet.')

@section('content')

    <nav aria-label="Breadcrumb" class="border-b border-black/10 py-3">
        <ol class="mx-auto max-w-7xl px-4 flex items-center gap-2 font-sans text-xs uppercase tracking-wide text-black/55">
            <li><a href="/" class="hover:text-black">Home</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page">{{ $category->type }}</li>
        </ol>
    </nav>

    <header class="mx-auto max-w-7xl px-4 py-8 border-b border-black">
        <p class="font-sans text-xs font-bold uppercase tracking-wide text-primary">Category</p>
        <h1 class="font-serif text-3xl md:text-4xl tracking-tight mt-1">{{ $category->type }}</h1>
    </header>

    <section class="mx-auto max-w-7xl px-4 py-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 divide-y sm:divide-y-0 lg:divide-x divide-black/10">
            @forelse ($posts as $post)
                <x-post-card
                    :post="$post"
                    :show-category="false"
                    class="pt-8 first:pt-0 sm:pt-0 lg:pl-8 lg:first:pl-0"
                />
                @if ($loop->index === 3 && ($ads['inline'] ?? collect())->isNotEmpty())
                    <div class="sm:col-span-2 lg:col-span-3">
                        <x-ad-slot :ads="$ads['inline']" position="inline" />
                    </div>
                @endif
            @empty
                <p class="font-serif text-black/50">No published posts in this category yet.</p>
            @endforelse
        </div>

        @if (method_exists($posts, 'links'))
            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @endif
    </section>

@endsection
