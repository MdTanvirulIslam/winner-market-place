<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found — {{ config('app.name') }}</title>
    <script>
        try {
            // Matches the storefront: light unless the visitor chose dark.
            if (localStorage.getItem('winnerTAM-dark') === 'true') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        } catch (e) {}
    </script>
    @vite('resources/css/app.css')
</head>
<body class="store-theme">
    <div class="error-wrap">
        <div class="error-card">
            <div class="error-code">404</div>
            <h1 class="error-title">Page not found</h1>
            <p class="error-text">The page you're looking for doesn't exist or may have been moved — perhaps the product was unpublished.</p>
            <a href="{{ route('home') }}" class="s-btn">Back to the store</a>
        </div>
    </div>
</body>
</html>
