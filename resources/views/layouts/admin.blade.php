<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($title) && $title ? $title . ' — ' : '' }}{{ config('app.name') }}</title>
    <script>
        // Apply the saved theme before first paint to avoid a light-mode flash.
        if (localStorage.getItem('winnerTAM-dark') === 'true') {
            document.documentElement.setAttribute('data-theme', 'dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    @include('partials.admin.topbar')

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    @include('partials.admin.sidebar')

    <div class="main-wrap" id="mainWrap">
        <main class="main-content" role="main">
            {{ $slot }}
        </main>
        <footer class="main-footer">
            <span>{{ date('Y') }} {{ config('app.name') }} admin panel.</span>
            <span>Phase 0</span>
        </footer>
    </div>

    <div class="offcanvas-overlay" id="offcanvasOverlay"></div>

    <aside class="offcanvas-panel" id="offcanvasPanel" role="complementary" aria-label="Settings panel">
        <div class="offcanvas-header">
            <h5>Preferences</h5>
            <button class="offcanvas-close" id="offcanvasClose" aria-label="Close settings"><span class="icon" data-icon="x"></span></button>
        </div>
        <div class="offcanvas-body">
            <div class="settings-section">
                <div class="settings-section-title">Appearance</div>
                <div class="settings-row">
                    <div class="settings-row-label"><span class="icon settings-icon" data-icon="moon"></span><span>Dark Mode</span></div>
                    <div class="toggle-switch" id="offcanvasDarkMode" role="switch" aria-label="Dark mode"></div>
                </div>
                <div class="mt-3.5">
                    <div class="settings-row-label mb-2.5"><span class="icon settings-icon" data-icon="palette"></span><span>Accent Color</span></div>
                    <div class="color-swatches">
                        <div class="color-swatch active" style="background:#0d9488;" data-color="teal"></div>
                        <div class="color-swatch" style="background:#3b82f6;" data-color="blue"></div>
                        <div class="color-swatch" style="background:#8b5cf6;" data-color="violet"></div>
                        <div class="color-swatch" style="background:#ec4899;" data-color="pink"></div>
                        <div class="color-swatch" style="background:#f59e0b;" data-color="amber"></div>
                        <div class="color-swatch" style="background:#ef4444;" data-color="red"></div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

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
