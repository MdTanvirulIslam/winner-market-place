<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <script>
            // Matches the storefront: dark unless the visitor chose light.
            if (localStorage.getItem('winnerTAM-dark') !== 'false') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="store-theme">
        <main class="min-h-screen bg-bg px-4 py-6 transition-colors duration-300">
            <div class="mx-auto flex min-h-[calc(100vh-48px)] max-w-6xl flex-col gap-8">
                <div class="flex items-center justify-between">
                    <a href="{{ route('home') }}" class="brand"><span class="icon" data-icon="badge-check"></span><span>{{ config('app.name') }}</span></a>
                    <button class="topbar-btn" id="darkModeToggle" aria-label="Toggle dark mode"><span class="icon" id="darkModeToggleIcon" data-icon="moon"></span></button>
                </div>

                <div class="flex flex-1 items-center justify-center">
                    <div class="animate-in opacity-0 w-full max-w-md rounded-[28px] border bg-card p-7 shadow-lg" style="border-color:var(--border);">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </main>

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
