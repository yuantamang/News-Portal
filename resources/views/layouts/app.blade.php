<!DOCTYPE html>
<html lang="en" data-theme="editorial">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- @yield echoes raw/unescaped; title and description can carry Post titles/context
         straight from the CMS, so they're escaped explicitly here rather than via @yield. --}}
    <meta name="description" content="{{ $__env->yieldContent('meta_description', 'Independent global news and analysis, published daily.') }}">
    <title>{{ $__env->yieldContent('title', 'Home') }} | The Broadsheet</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @vite(['resources/css/app.css'])
    @stack('head')
</head>
<body class="min-h-screen bg-base-100 font-sans text-base-content antialiased flex flex-col">

<a href="#main-content" class="sr-only focus:not-sr-only focus:fixed focus:top-2 focus:left-2 focus:z-[100] focus:bg-black focus:text-white focus:px-4 focus:py-2 focus:text-sm">
    Skip to content
</a>

{{--
    Data every page implicitly shares (wire via a View Composer rather than
    passing from each controller action):

    $navCategories   Collection<Category>   {type, slug} — drives nav + footer
    $breakingPost    Post|null              most recent post where is_breaking = true
    $ads             array<string, Collection<Advertisement>> keyed by position
                     (header/sidebar/footer/inline), pre-filtered to
                     status = active and within the start_at/end_at window
    $contacts        Collection<Contact>    {phone_number, email, link}
--}}

<header id="top" class="border-b border-black/10">
    {{-- Utility bar --}}
    <div class="hidden md:block border-b border-black/10">
        <div class="mx-auto max-w-7xl px-4 flex items-center justify-between py-2 font-sans text-[11px] font-medium uppercase tracking-wide text-black/60">
            <p>{{ now()->format('l, F j, Y') }}</p>
            <nav aria-label="Utility">
                <ul class="flex items-center gap-4">
                    <li><a href="/account/login" class="hover:text-black">Sign In</a></li>
                </ul>
            </nav>
        </div>
    </div>

    {{-- Masthead --}}
    <div class="mx-auto max-w-7xl px-4 py-6 text-center border-b border-black">
        <a href="/" class="inline-block font-serif text-4xl md:text-5xl font-bold tracking-tight text-black" aria-label="The Broadsheet — Home">
            The Broadsheet
        </a>
        <p class="font-sans text-[11px] font-semibold uppercase tracking-[0.2em] text-black/50 mt-2">
            Global News &amp; Analysis
        </p>
    </div>

    {{-- Primary navigation — category list is real Category data, not a fixed set of section names --}}
    <div class="mx-auto max-w-7xl px-4">
        <div class="flex items-center justify-between py-1 gap-4">
            <nav aria-label="Primary" class="min-w-0">
                <ul class="hidden lg:flex items-center gap-7 font-sans text-xs font-bold uppercase tracking-wide">
                    @forelse ($navCategories ?? [] as $category)
                        <li>
                            <a href="/news/category/{{ $category->slug }}"
                               class="inline-block py-3 border-b-2 border-transparent hover:border-primary transition-colors">
                                {{ $category->type }}
                            </a>
                        </li>
                    @empty
                        {{-- No categories yet — nav renders empty rather than a fabricated list. --}}
                    @endforelse
                </ul>
            </nav>

            <div class="hidden lg:block w-56 flex-shrink-0">
                <x-search-input id="nav-search" />
            </div>

            <button
                id="nav-toggle"
                type="button"
                class="lg:hidden btn btn-ghost btn-square rounded-none -ml-3"
                aria-expanded="false"
                aria-controls="mobile-nav"
            >
                <span class="sr-only">Open menu</span>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M3 6h18M3 12h18M3 18h18" stroke-linecap="round" />
                </svg>
            </button>

            <a href="/search" class="lg:hidden btn btn-ghost btn-square rounded-none" aria-label="Search">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="11" cy="11" r="7" />
                    <path d="M21 21l-4.3-4.3" stroke-linecap="round" />
                </svg>
            </a>
        </div>

        <ul id="mobile-nav" hidden class="lg:hidden border-t border-black/10 py-2 flex flex-col font-sans text-sm font-bold uppercase tracking-wide">
            @forelse ($navCategories ?? [] as $category)
                <li>
                    <a href="/news/category/{{ $category->slug }}" class="block py-2.5 border-b border-black/5">
                        {{ $category->type }}
                    </a>
                </li>
            @empty
                <li class="py-2.5 text-black/40 normal-case font-normal">No categories yet.</li>
            @endforelse
        </ul>
    </div>
</header>

<x-breaking-news-banner :post="$breakingPost ?? null" />

@if (($ads['header'] ?? collect())->isNotEmpty())
    <div class="mx-auto max-w-7xl px-4 py-4">
        <x-ad-slot :ads="$ads['header']" position="header" />
    </div>
@endif

<main id="main-content" class="flex-1">
    @yield('content')
</main>

@if (($ads['footer'] ?? collect())->isNotEmpty())
    <div class="mx-auto max-w-7xl px-4 py-6 border-t border-black/10">
        <x-ad-slot :ads="$ads['footer']" position="footer" />
    </div>
@endif

<footer class="border-t border-black mt-8">
    <div class="mx-auto max-w-7xl px-4 py-10 grid grid-cols-2 md:grid-cols-5 gap-x-8 gap-y-10">
        <div class="col-span-2 md:col-span-2" id="contact-info">
            <a href="/" class="font-serif text-2xl font-bold text-black">The Broadsheet</a>
            <p class="font-serif text-sm text-black/60 mt-3 max-w-xs leading-relaxed">
                Independent reporting and analysis, published daily since digital's first edition.
            </p>

            @forelse ($contacts ?? [] as $contact)
                <ul class="mt-4 space-y-1 font-sans text-sm text-black/70">
                    @if ($contact->phone_number)
                        <li><a href="tel:{{ $contact->phone_number }}" class="hover:text-black hover:underline underline-offset-2">{{ $contact->phone_number }}</a></li>
                    @endif
                    @if ($contact->email)
                        <li><a href="mailto:{{ $contact->email }}" class="hover:text-black hover:underline underline-offset-2">{{ $contact->email }}</a></li>
                    @endif
                    @if ($contact->link)
                        <li><a href="{{ $contact->link }}" class="hover:text-black hover:underline underline-offset-2" target="_blank" rel="noopener">{{ $contact->link }}</a></li>
                    @endif
                </ul>
            @empty
                {{-- No Contact records yet. --}}
            @endforelse
        </div>

        <nav aria-label="Sections">
            <h2 class="font-sans text-xs font-bold uppercase tracking-wide mb-3">Sections</h2>
            <ul class="space-y-2 font-sans text-sm text-black/70">
                @forelse ($navCategories ?? [] as $category)
                    <li>
                        <a href="/news/category/{{ $category->slug }}" class="hover:text-black hover:underline underline-offset-2">
                            {{ $category->type }}
                        </a>
                    </li>
                @empty
                    <li class="text-black/40">No categories yet.</li>
                @endforelse
            </ul>
        </nav>

        <nav aria-label="Company">
            <h2 class="font-sans text-xs font-bold uppercase tracking-wide mb-3">Company</h2>
            <ul class="space-y-2 font-sans text-sm text-black/70">
                <li><a href="/about" class="hover:text-black hover:underline underline-offset-2">About Us</a></li>
                <li><a href="/careers" class="hover:text-black hover:underline underline-offset-2">Careers</a></li>
                <li><a href="/press" class="hover:text-black hover:underline underline-offset-2">Press</a></li>
            </ul>
        </nav>

        <nav aria-label="Legal">
            <h2 class="font-sans text-xs font-bold uppercase tracking-wide mb-3">Legal</h2>
            <ul class="space-y-2 font-sans text-sm text-black/70">
                <li><a href="/privacy" class="hover:text-black hover:underline underline-offset-2">Privacy Policy</a></li>
                <li><a href="/terms" class="hover:text-black hover:underline underline-offset-2">Terms of Service</a></li>
                <li><a href="/accessibility" class="hover:text-black hover:underline underline-offset-2">Accessibility</a></li>
            </ul>
        </nav>
    </div>

    <div class="border-t border-black/10">
        <div class="mx-auto max-w-7xl px-4 py-4 flex flex-col sm:flex-row items-center justify-between gap-2 font-sans text-[11px] text-black/50">
            <p>&copy; {{ now()->year }} The Broadsheet. All rights reserved.</p>
            <a href="#top" class="hover:text-black hover:underline underline-offset-2">Back to top</a>
        </div>
    </div>
</footer>

<script>
    (function () {
        var toggle = document.getElementById('nav-toggle');
        var panel = document.getElementById('mobile-nav');
        if (!toggle || !panel) return;
        toggle.addEventListener('click', function () {
            var isOpen = !panel.hidden;
            panel.hidden = isOpen;
            toggle.setAttribute('aria-expanded', String(!isOpen));
        });
    })();
</script>
@stack('scripts')

</body>
</html>
