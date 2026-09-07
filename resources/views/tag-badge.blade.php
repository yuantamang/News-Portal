@props(['tag'])

<a
    href="/news/tag/{{ $tag->slug }}"
    @if ($tag->color)
        style="border-color: {{ $tag->color }}; color: {{ $tag->color }};"
    @endif
    class="badge badge-outline rounded-none px-3 py-3 font-sans text-[11px] uppercase tracking-wide hover:border-black hover:text-black
        {{ $tag->color ? '' : 'border-black/25 text-black/65' }}"
>
    {{ $tag->title }}
</a>
