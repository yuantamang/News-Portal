{{--
    Page-specific data (in addition to the layout's shared $navCategories,
    $breakingPost, $ads, $contacts):

    $tag    Tag                          {title, slug, context, color} — context/color are nullable
    $posts  LengthAwarePaginator<Post>   published posts carrying this tag
--}}
@extends('layouts.app')

@section('title', $tag->title)
@section('meta_description', 'Stories tagged ' . $tag->title . ' from The Broadsheet.')

@section('content')

    <nav aria-label="Breadcrumb" class="border-b border-black/10 py-3">
        <ol class="mx-auto max-w-7xl px-4 flex items-center gap-2 font-sans text-xs uppercase tracking-wide text-black/55">
            <li><a href="/" class="hover:text-black">Home</a></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page">Tag: {{ $tag->title }}</li>
        </ol>
    </nav>

    <header
        class="mx-auto max-w-7xl px-4 py-8 border-b border-black"
        @if ($tag->color) style="border-bottom-color: {{ $tag->color }};" @endif
    >
        <p class="font-sans text-xs font-bold uppercase tracking-wide text-black/50">Tag</p>
        <h1
            class="font-serif text-3xl md:text-4xl tracking-tight mt-1"
            @if ($tag->color) style="color: {{ $tag->color }};" @endif
        >
            {{ $tag->title }}
        </h1>

        @if ($tag->context)
            <div class="font-serif text-black/70 leading-relaxed max-w-2xl mt-4">
                {!! $tag->context !!}
            </div>
        @endif
    </header>

    <section class="mx-auto max-w-7xl px-4 py-8">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 divide-y sm:divide-y-0 lg:divide-x divide-black/10">
            @forelse ($posts as $post)
                <x-post-card
                    :post="$post"
                    class="pt-8 first:pt-0 sm:pt-0 lg:pl-8 lg:first:pl-0"
                />
                @if ($loop->index === 3 && ($ads['inline'] ?? collect())->isNotEmpty())
                    <div class="sm:col-span-2 lg:col-span-3">
                        <x-ad-slot :ads="$ads['inline']" position="inline" />
                    </div>
                @endif
            @empty
                <p class="font-serif text-black/50">No published posts carry this tag yet.</p>
            @endforelse
        </div>

        @if (method_exists($posts, 'links'))
            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @endif
    </section>

@endsection
