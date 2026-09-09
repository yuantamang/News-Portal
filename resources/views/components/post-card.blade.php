{{--
    Expects a real (or Eloquent-shaped) Post: title, slug, image, context,
    published_at, view_count, is_breaking, categories (Collection<Category>).
    No author, no dek, no read-time — Post has none of these fields.
--}}
@props([
    'post',
    'size' => 'md', // sm | md | lg
    'showCategory' => true,
])

@php
    $titleSize = match ($size) {
        'lg' => 'text-2xl md:text-3xl',
        'sm' => 'text-base',
        default => 'text-lg',
    };
    $primaryCategory = $post->categories->first() ?? null;
    $excerpt = \Illuminate\Support\Str::limit(strip_tags($post->context ?? ''), 110);
@endphp

<article {{ $attributes->merge(['class' => 'group']) }}>
    @if ($post->image)
        <a href="/news/{{ $post->slug }}" class="block border border-black/10 overflow-hidden mb-3" tabindex="-1" aria-hidden="true">
            <img
                src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($post->image) }}"
                alt=""
                loading="lazy"
                class="w-full aspect-[4/3] object-cover transition-transform duration-300 group-hover:scale-[1.02]"
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
        <a href="/news/{{ $post->slug }}" class="hover:underline decoration-black/30 underline-offset-4">
            {{ $post->title }}
        </a>
    </h3>

    @if ($excerpt)
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
