@props(['category'])

@if ($category)
    <a
        href="{{ route('news.category', $category->slug) }}"
        class="font-sans text-[11px] font-bold uppercase tracking-wide text-primary hover:underline underline-offset-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary"
    >
        {{ $category->type }}
    </a>
@endif
