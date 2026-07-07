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
        // The storefront is light-first: dark only when the visitor chose it.
        if (localStorage.getItem('winnerTAM-dark') === 'true') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="store-theme">
    <header class="s-topbar" x-data="{ mobileOpen: false }">
        <div class="mx-auto flex h-16 max-w-6xl items-center justify-between gap-4 px-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="s-brand-badge"><span class="icon" data-icon="badge-check"></span></span>
                <span class="font-heading text-[17px] font-extrabold tracking-tight text-text">{{ config('app.name') }}</span>
            </a>

            <nav class="hidden items-center gap-7 md:flex">
                <a href="{{ route('home') }}" class="s-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('store.products') }}" class="s-nav-link {{ request()->routeIs('store.products*') ? 'active' : '' }}">Products</a>
                <a href="{{ route('store.about') }}" class="s-nav-link {{ request()->routeIs('store.about') ? 'active' : '' }}">About</a>
                <a href="{{ route('store.contact') }}" class="s-nav-link {{ request()->routeIs('store.contact') ? 'active' : '' }}">Contact</a>
            </nav>

            <div class="flex items-center gap-2">
                <button class="topbar-btn" id="darkModeToggle" aria-label="Toggle dark mode"><span class="icon" id="darkModeToggleIcon" data-icon="moon"></span></button>
                @auth
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" class="s-btn hidden !px-4 !py-2 md:inline-flex">Admin Panel</a>
                    @else
                        <a href="{{ route('account.orders') }}" class="s-btn hidden !px-4 !py-2 md:inline-flex">My Account</a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="s-nav-link hidden px-2 md:inline">Login</a>
                    <a href="{{ route('register') }}" class="s-btn hidden !px-4 !py-2 md:inline-flex">Get Started</a>
                @endauth
                <button type="button" class="topbar-btn md:hidden" @click="mobileOpen = !mobileOpen" aria-label="Toggle menu">
                    <span class="icon" data-icon="menu" x-show="!mobileOpen"></span>
                    <span class="icon" data-icon="x" x-show="mobileOpen" x-cloak></span>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div x-show="mobileOpen" x-transition.opacity.duration.150ms x-cloak class="border-t border-border md:hidden">
            <nav class="mx-auto flex max-w-6xl flex-col gap-1 px-4 py-4">
                <a href="{{ route('home') }}" class="s-nav-link rounded-lg px-3 py-2.5 {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('store.products') }}" class="s-nav-link rounded-lg px-3 py-2.5 {{ request()->routeIs('store.products*') ? 'active' : '' }}">Products</a>
                <a href="{{ route('store.about') }}" class="s-nav-link rounded-lg px-3 py-2.5 {{ request()->routeIs('store.about') ? 'active' : '' }}">About</a>
                <a href="{{ route('store.contact') }}" class="s-nav-link rounded-lg px-3 py-2.5 {{ request()->routeIs('store.contact') ? 'active' : '' }}">Contact</a>
                <div class="mt-3 flex items-center gap-2">
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="s-btn flex-1 !py-2.5">Admin Panel</a>
                        @else
                            <a href="{{ route('account.orders') }}" class="s-btn flex-1 !py-2.5">My Account</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="s-btn-ghost flex-1 !py-2.5">Login</a>
                        <a href="{{ route('register') }}" class="s-btn flex-1 !py-2.5">Get Started</a>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

    <main class="min-h-[70vh] bg-bg transition-colors duration-300">
        {{ $slot }}
    </main>

    <footer class="bg-bg">
        <div class="s-footer-line"></div>

        {{-- Contact strip --}}
        <div class="mx-auto grid max-w-6xl gap-6 border-b border-border px-4 py-10 sm:grid-cols-3">
            <a href="{{ route('store.contact') }}" class="group flex items-center gap-3.5">
                <span class="s-contact-icon"><span class="icon" data-icon="mail"></span></span>
                <span>
                    <span class="block text-[13px] font-bold text-text">Contact Us</span>
                    <span class="block text-[12px] text-muted transition-colors duration-300 group-hover:text-accent-light">Presales &amp; support — we reply fast</span>
                </span>
            </a>
            <a href="{{ route('account.downloads') }}" class="group flex items-center gap-3.5">
                <span class="s-contact-icon"><span class="icon" data-icon="download"></span></span>
                <span>
                    <span class="block text-[13px] font-bold text-text">Instant Delivery</span>
                    <span class="block text-[12px] text-muted transition-colors duration-300 group-hover:text-accent-light">License &amp; downloads right after payment</span>
                </span>
            </a>
            <a href="{{ route('store.refund-policy') }}" class="group flex items-center gap-3.5">
                <span class="s-contact-icon"><span class="icon" data-icon="shield"></span></span>
                <span>
                    <span class="block text-[13px] font-bold text-text">Buyer Protection</span>
                    <span class="block text-[12px] text-muted transition-colors duration-300 group-hover:text-accent-light">Clear refund policy on every purchase</span>
                </span>
            </a>
        </div>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 py-14 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <a href="{{ route('home') }}" class="mb-4 flex items-center gap-2.5">
                    <span class="s-brand-badge"><span class="icon" data-icon="badge-check"></span></span>
                    <span class="font-heading text-[17px] font-extrabold tracking-tight text-text">{{ config('app.name') }}</span>
                </a>
                <p class="text-[13px] leading-6 text-muted">Premium web applications by Winner Devs — licensed, updated, and delivered automatically.</p>
            </div>
            <div>
                <h5 class="mb-4 text-[13px] font-bold uppercase tracking-[0.14em] text-text">Store</h5>
                <div class="space-y-2.5 text-[13px] font-medium text-muted">
                    <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('store.products') }}">All Products</a>
                    <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('store.about') }}">About Us</a>
                    <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('store.contact') }}">Contact</a>
                </div>
            </div>
            <div>
                <h5 class="mb-4 text-[13px] font-bold uppercase tracking-[0.14em] text-text">Account</h5>
                <div class="space-y-2.5 text-[13px] font-medium text-muted">
                    @auth
                        <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('account.orders') }}">My Purchases</a>
                        <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('account.downloads') }}">Downloads</a>
                    @else
                        <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('login') }}">Login</a>
                        <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('register') }}">Register</a>
                    @endauth
                </div>
            </div>
            <div>
                <h5 class="mb-4 text-[13px] font-bold uppercase tracking-[0.14em] text-text">Legal</h5>
                <div class="space-y-2.5 text-[13px] font-medium text-muted">
                    <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('store.terms') }}">Terms of Service</a>
                    <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('store.privacy') }}">Privacy Policy</a>
                    <a class="block transition-colors duration-300 hover:text-accent-light" href="{{ route('store.refund-policy') }}">Refund Policy</a>
                </div>
            </div>
        </div>
        <div class="border-t border-border py-5">
            <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-3 px-4 text-[12px] text-muted">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }} · Powered by Winner Devs</span>
                <span class="inline-flex items-center gap-1.5"><span class="icon text-accent-light" data-icon="lock-keyhole"></span> Secure payments via SSLCommerz — bKash, Nagad, Rocket &amp; cards</span>
            </div>
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
