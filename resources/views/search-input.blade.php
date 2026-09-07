@props(['query' => null])

<form action="/search" method="GET" role="search" {{ $attributes->except('id')->merge(['class' => 'flex']) }}>
    <label for="{{ $attributes->get('id', 'search-input') }}" class="sr-only">Search articles</label>
    <input
        id="{{ $attributes->get('id', 'search-input') }}"
        type="search"
        name="q"
        value="{{ $query }}"
        placeholder="Search articles…"
        class="input input-bordered rounded-none flex-1 border-black/20 focus:border-black text-sm"
    >
    <button type="submit" class="btn btn-neutral rounded-none" aria-label="Search">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
            <circle cx="11" cy="11" r="7" />
            <path d="M21 21l-4.3-4.3" stroke-linecap="round" />
        </svg>
    </button>
</form>
