<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name') }}</title>
    <script>
        if (localStorage.getItem('winnerTAM-dark') === 'true') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <main class="min-h-screen bg-bg px-4 py-6 transition-colors duration-300">
        <div class="mx-auto flex min-h-[calc(100vh-48px)] max-w-6xl flex-col gap-8">
            <div class="flex items-center justify-between">
                <a href="{{ route('home') }}" class="brand"><span class="icon" data-icon="badge-check"></span><span>{{ config('app.name') }}</span></a>
                <div class="flex items-center gap-2">
                    <button class="topbar-btn" id="darkModeToggle" aria-label="Toggle dark mode"><span class="icon" id="darkModeToggleIcon" data-icon="moon"></span></button>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.dashboard') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Admin Panel</a>
                        @else
                            <a href="{{ route('profile.edit') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">My Account</a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-text">Login</a>
                        <a href="{{ route('register') }}" class="rounded-lg bg-accent px-4 py-2 text-sm font-semibold text-white transition-colors duration-300 hover:bg-accent-hover">Register</a>
                    @endauth
                </div>
            </div>

            <div class="flex flex-1 items-center justify-center">
                <div class="animate-in opacity-0 max-w-2xl text-center">
                    <div class="mb-4 inline-flex rounded-full bg-accent/10 px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.22em] text-accent">Opening Soon</div>
                    <h1 class="font-heading text-4xl font-extrabold tracking-[-0.04em] text-text sm:text-5xl">Premium web applications for your business</h1>
                    <p class="mt-4 text-[15px] leading-7 text-muted">
                        News portals, POS software, inventory management, HRM, and more —
                        built by Winner Devs, licensed and delivered automatically.
                        The store launches with our product catalog in the next phase.
                    </p>
                </div>
            </div>

            <footer class="flex items-center justify-between border-t pt-4 text-[13px] text-muted" style="border-color:var(--border);">
                <span>&copy; {{ date('Y') }} {{ config('app.name') }}</span>
                <span>Powered by Winner Devs</span>
            </footer>
        </div>
    </main>
</body>
</html>
