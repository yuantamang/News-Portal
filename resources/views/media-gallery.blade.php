{{--
    Renders the polymorphic `media` relationship (distinct from Post's own
    `image` field, which is the single main/featured image and is rendered
    separately by the page, not here).

    file_path is cast to an array on the Media model (Filament's uploader
    supports multiple files per record). video_url is assumed to already be
    an embeddable URL (e.g. a YouTube/Vimeo embed link) — the backend
    documentation doesn't specify the exact format, so this renders it as-is
    in an iframe.
--}}
@props(['media'])

@php $media = $media ?? collect(); @endphp

@if ($media->isNotEmpty())
    <div {{ $attributes->merge(['class' => 'space-y-8']) }}>
        @foreach ($media as $item)
            <figure>
                @if ($item->type === 'image')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach ((array) $item->file_path as $path)
                            <img src="{{ $path }}" alt="{{ $item->caption ?? '' }}" class="w-full border border-black/10">
                        @endforeach
                    </div>
                @elseif ($item->type === 'video')
                    <div class="aspect-video border border-black/10">
                        <iframe
                            src="{{ $item->video_url }}"
                            class="w-full h-full"
                            loading="lazy"
                            allowfullscreen
                            title="{{ $item->caption ?? 'Embedded video' }}"
                        ></iframe>
                    </div>
                @elseif ($item->type === 'file')
                    <div class="flex flex-wrap gap-2">
                        @foreach ((array) $item->file_path as $path)
                            <a
                                href="{{ $path }}"
                                target="_blank"
                                rel="noopener"
                                class="inline-flex items-center gap-2 border border-black/15 px-4 py-2 font-sans text-sm hover:border-black"
                            >
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path d="M12 3v12m0 0-4-4m4 4 4-4" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2" stroke-linecap="round" />
                                </svg>
                                {{ $item->caption ?? 'Download attachment' }}
                            </a>
                        @endforeach
                    </div>
                @endif

                @if (($item->caption ?? null) && $item->type !== 'file')
                    <figcaption class="font-sans text-xs text-black/55 mt-2">{{ $item->caption }}</figcaption>
                @endif
            </figure>
        @endforeach
    </div>
@endif
