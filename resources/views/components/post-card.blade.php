{{--
    Expects a real (or Eloquent-shaped) Post: id, title, slug, image, context,
    published_at, view_count, is_breaking, categories (Collection<Category>).
    No author, no dek, no read-time — Post has none of these fields.
--}}
@props([
    'post',
    'size' => 'md', // sm | md | lg
    'layout' => 'vertical', // vertical (image on top) | horizontal (image at side, compact)
    'showCategory' => true,
    'showImage' => true, // set false for dense, text-only headline lists
    'showExcerpt' => true, // set false alongside showImage for the same dense lists
])

@php
    $titleSize = match ($size) {
        'lg' => 'text-2xl md:text-3xl',
        'sm' => 'text-base',
        default => 'text-lg',
    };
    $imageAspect = $size === 'lg' ? 'aspect-[16/9]' : 'aspect-[4/3]';
    $primaryCategory = $post->categories->first() ?? null;
    $excerpt = \Illuminate\Support\Str::limit(strip_tags($post->context ?? ''), 110);
    $href = route('news.show', $post->slug);
@endphp

@if ($layout === 'horizontal')
    <article {{ $attributes->merge(['class' => 'group flex gap-4']) }}>
        @if ($showImage && $post->image)
            <a href="{{ $href }}" class="block w-24 h-24 flex-shrink-0 border border-black/10 overflow-hidden" tabindex="-1" aria-hidden="true">
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->image) }}"
                    alt=""
                    loading="lazy"
                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-[1.05]"
                >
            </a>
        @endif
        <div class="min-w-0 flex-1 flex flex-col justify-center">
            <div class="flex items-center gap-2 flex-wrap">
                @if ($post->is_breaking ?? false)
                    <span class="font-sans text-[10px] font-bold uppercase tracking-wide bg-primary text-primary-content px-1.5 py-0.5">Breaking</span>
                @endif
                @if ($showCategory && $primaryCategory)
                    <x-category-badge :category="$primaryCategory" />
                @endif
            </div>
            <h3 class="font-serif {{ $titleSize }} leading-snug mt-1">
                <a href="{{ $href }}" class="hover:underline decoration-black/30 underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                    {{ $post->title }}
                </a>
            </h3>
            @if ($showExcerpt && $excerpt)
                <p class="font-serif text-sm text-black/60 leading-snug mt-1 line-clamp-2">{{ $excerpt }}</p>
            @endif
            <div class="flex items-center gap-3 font-sans text-[11px] text-black/45 uppercase tracking-wide mt-1.5">
                @if ($post->published_at ?? null)
                    <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->diffForHumans() }}</time>
                @endif
                @if (! is_null($post->view_count ?? null))
                    <span class="border-l border-black/15 pl-3">{{ number_format($post->view_count) }} views</span>
                @endif
            </div>
        </div>
    </article>
@else
    <article {{ $attributes->merge(['class' => 'group']) }}>
        @if ($showImage && $post->image)
            <a href="{{ $href }}" class="block border border-black/10 overflow-hidden mb-3" tabindex="-1" aria-hidden="true">
                <img
                    src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->image) }}"
                    alt=""
                    loading="lazy"
                    class="w-full {{ $imageAspect }} object-cover transition-transform duration-300 group-hover:scale-[1.02]"
                >
            </a>
        @endif

        <div class="flex items-center gap-2 flex-wrap">
            @if ($post->is_breaking ?? false)
                <span class="font-sans text-[10px] font-bold uppercase tracking-wide bg-primary text-primary-content px-1.5 py-0.5">
                    Breaking
                </span>
            @endif
            @if ($showCategory && $primaryCategory)
                <x-category-badge :category="$primaryCategory" />
            @endif
        </div>

        <h3 class="font-serif {{ $titleSize }} leading-snug mt-1">
            <a href="{{ $href }}" class="hover:underline decoration-black/30 underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary">
                {{ $post->title }}
            </a>
        </h3>

        @if ($showExcerpt && $excerpt)
            <p class="font-serif text-sm text-black/60 leading-snug mt-1">{{ $excerpt }}</p>
        @endif

        <div class="flex items-center gap-3 font-sans text-[11px] text-black/45 uppercase tracking-wide mt-2">
            @if ($post->published_at ?? null)
                <time datetime="{{ $post->published_at->toIso8601String() }}">{{ $post->published_at->diffForHumans() }}</time>
            @endif
            @if (! is_null($post->view_count ?? null))
                <span class="border-l border-black/15 pl-3">{{ number_format($post->view_count) }} views</span>
            @endif
        </div>
    </article>
@endif
