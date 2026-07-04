<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) && $title ? $title . ' — ' : '' }}{{ config('app.name') }}</title>
    @if(!empty($metaDescription))
        <meta name="description" content="{{ $metaDescription }}">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ isset($title) && $title ? $title . ' — ' : '' }}{{ config('app.name') }}">
    @if(!empty($metaDescription))
        <meta property="og:description" content="{{ $metaDescription }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    @if(!empty($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif
    <script>
        if (localStorage.getItem('winnerTAM-dark') === 'true') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <header class="sticky top-0 z-40 border-b" style="background:var(--bg-topbar);border-color:var(--border);">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4">
            <a href="{{ route('home') }}" class="brand"><span class="icon" data-icon="badge-check"></span><span>{{ config('app.name') }}</span></a>
            <nav class="hidden items-center gap-6 text-sm font-semibold text-text md:flex">
                <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-accent' : '' }}">Home</a>
                <a href="{{ route('store.products') }}" class="{{ request()->routeIs('store.products*') ? 'text-accent' : '' }}">Products</a>
                <a href="{{ route('store.about') }}" class="{{ request()->routeIs('store.about') ? 'text-accent' : '' }}">About</a>
                <a href="{{ route('store.contact') }}" class="{{ request()->routeIs('store.contact') ? 'text-accent' : '' }}">Contact</a>
            </nav>
            <div class="flex items-center gap-2">
                <button class="topbar-btn" id="darkModeToggle" aria-label="Toggle dark mode"><span class="icon" id="darkModeToggleIcon" data-icon="moon"></span></button>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Admin Panel</a>
                    @else
                        <a href="{{ route('account.orders') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">My Account</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-text">Login</a>
                    <a href="{{ route('register') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Register</a>
                @endauth
            </div>
        </div>
    </header>

    <main class="min-h-[70vh] bg-bg transition-colors duration-300">
        {{ $slot }}
    </main>

    <footer class="border-t" style="background:var(--bg-topbar);border-color:var(--border);">
        <div class="mx-auto grid max-w-6xl gap-8 px-4 py-10 md:grid-cols-3">
            <div>
                <div class="brand mb-3"><span class="icon" data-icon="badge-check"></span><span>{{ config('app.name') }}</span></div>
                <p class="text-[13px] leading-6 text-muted">Premium web applications by Winner Devs — licensed, updated, and delivered automatically.</p>
            </div>
            <div>
                <h5 class="mb-3 text-sm font-bold text-text">Store</h5>
                <div class="space-y-2 text-[13px] text-muted">
                    <a class="block hover:text-accent" href="{{ route('store.products') }}">All Products</a>
                    <a class="block hover:text-accent" href="{{ route('store.about') }}">About Us</a>
                    <a class="block hover:text-accent" href="{{ route('store.contact') }}">Contact</a>
                    @auth
                        <a class="block hover:text-accent" href="{{ route('account.orders') }}">My Account</a>
                    @else
                        <a class="block hover:text-accent" href="{{ route('login') }}">Login</a>
                        <a class="block hover:text-accent" href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            </div>
            <div>
                <h5 class="mb-3 text-sm font-bold text-text">Legal</h5>
                <div class="space-y-2 text-[13px] text-muted">
                    <a class="block hover:text-accent" href="{{ route('store.terms') }}">Terms of Service</a>
                    <a class="block hover:text-accent" href="{{ route('store.privacy') }}">Privacy Policy</a>
                    <a class="block hover:text-accent" href="{{ route('store.refund-policy') }}">Refund Policy</a>
                </div>
            </div>
        </div>
        <div class="border-t py-4 text-center text-[12px] text-muted" style="border-color:var(--border);">
            &copy; {{ date('Y') }} {{ config('app.name') }} · Powered by Winner Devs
        </div>
    </footer>

    <script>
        window.__flash = {
            success: @json(session('success')),
            error: @json(session('error')),
            warning: @json(session('warning')),
            info: @json(session('info')),
        };
    </script>
</body>
</html>
