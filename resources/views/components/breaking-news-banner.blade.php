@props(['post'])

@if ($post)
    <div class="bg-primary text-primary-content border-b border-black" role="region" aria-label="Breaking news">
        <div class="mx-auto max-w-7xl px-4 py-2.5 flex items-center gap-3" aria-live="polite">
            <span class="flex items-center gap-1.5 font-sans text-[11px] font-bold uppercase tracking-wide flex-shrink-0">
                <span class="relative flex w-1.5 h-1.5 flex-shrink-0" aria-hidden="true">
                    <span class="absolute inline-flex w-full h-full bg-primary-content opacity-75 animate-ping motion-reduce:hidden"></span>
                    <span class="relative inline-flex w-1.5 h-1.5 bg-primary-content"></span>
                </span>
                Breaking
            </span>
            <a href="{{ route('news.show', $post->slug) }}" class="font-sans text-sm font-semibold truncate hover:underline underline-offset-2 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-content">
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
