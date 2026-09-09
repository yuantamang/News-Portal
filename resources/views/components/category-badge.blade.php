@props(['category'])

@if ($category)
    <a
        href="/news/category/{{ $category->slug }}"
        class="font-sans text-[11px] font-bold uppercase tracking-wide text-primary hover:underline underline-offset-2"
    >
        {{ $category->type }}
    </a>
@endif
