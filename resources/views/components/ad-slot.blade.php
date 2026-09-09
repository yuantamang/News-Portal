{{--
    $ads must already be filtered to this position, status = active, and the
    start_at/end_at date window — that filtering is a controller/query concern,
    not something this view performs.
--}}
@props([
    'ads',
    'position', // header | sidebar | footer | inline | popup — labeling only
])

@php $ads = $ads ?? collect(); @endphp

@if ($ads->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'border border-black/10 p-3']) }} data-ad-position="{{ $position }}">
        <p class="font-sans text-[10px] uppercase tracking-wide text-black/40 mb-2">Advertisement</p>
        <div class="space-y-3">
            @foreach ($ads as $ad)
                <a
                    href="{{ $ad->link }}"
                    target="_blank"
                    rel="sponsored noopener"
                    class="block border border-black/10 hover:border-black/30"
                    aria-label="{{ $ad->title }}"
                >
                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($ad->image) }}" alt="{{ $ad->title }}" class="w-full object-cover">
                </a>
            @endforeach
        </div>
    </div>
@endif
