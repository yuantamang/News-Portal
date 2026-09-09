@props(['post'])

@if ($post)
    <div class="bg-primary text-primary-content border-b border-black">
        <div class="mx-auto max-w-7xl px-4 py-2.5 flex items-center gap-3">
            <span class="flex items-center gap-1.5 font-sans text-[11px] font-bold uppercase tracking-wide flex-shrink-0">
                <span class="w-1.5 h-1.5 bg-primary-content inline-block" aria-hidden="true"></span>
                Breaking
            </span>
            <a href="/news/{{ $post->slug }}" class="font-sans text-sm font-semibold truncate hover:underline underline-offset-2">
                {{ $post->title }}
            </a>
            @if ($post->published_at ?? null)
                <span class="ml-auto hidden sm:inline font-sans text-[11px] text-primary-content/70 flex-shrink-0">
                    {{ $post->published_at->diffForHumans() }}
                </span>
            @endif
        </div>
    </div>
@endif
