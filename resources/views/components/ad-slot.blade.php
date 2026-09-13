{{--
    $ads must already be filtered to this position, status = active, and the
    start_at/end_at date window — that filtering is a controller/query concern,
    not something this view performs.

    Every ad renders inside a fixed-height box regardless of position or the
    creative's native dimensions. Real ad units are always a bounded size
    (IAB standards like 300x250, 728x90) — nothing here lets an arbitrarily
    tall or wide uploaded image dictate page layout the way an unconstrained
    image would.

    Layout varies by position: header/footer/inline slots are conventionally
    wide horizontal banners, so multiple ads lay out in a row; sidebar/popup
    slots are conventionally narrower, so they stack.
--}}
@props([
    'ads',
    'position', // header | sidebar | footer | inline | popup — labeling only
])

@php
    $ads = $ads ?? collect();
    $horizontal = in_array($position, ['header', 'footer', 'inline'], true);
    $boxHeight = $horizontal ? 'h-24 sm:h-28' : 'h-44 sm:h-52';
@endphp

@if ($ads->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'border border-black/10 p-3']) }} data-ad-position="{{ $position }}">
        <p class="font-sans text-[10px] font-semibold uppercase tracking-wide text-black/40 mb-2">Advertisement</p>
        <div class="{{ $horizontal ? 'flex flex-wrap items-start gap-3' : 'space-y-3' }}">
            @foreach ($ads as $ad)
                <a
                    href="{{ $ad->link }}"
                    target="_blank"
                    rel="sponsored noopener"
                    class="block border border-black/10 hover:border-black/30 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary transition-colors overflow-hidden {{ $boxHeight }} {{ $horizontal ? 'w-full sm:w-80' : '' }}"
                    aria-label="{{ $ad->title }}"
                >
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($ad->image) }}" alt="{{ $ad->title }}" class="w-full h-full object-cover">
                </a>
            @endforeach
        </div>
    </div>
@endif
