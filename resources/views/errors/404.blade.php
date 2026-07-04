<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found — {{ config('app.name') }}</title>
    <script>
        try {
            if (localStorage.getItem('winnerTAM-dark') === 'true') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        } catch (e) {}
    </script>
    {{-- Standalone styles: error pages must render even if Vite assets fail. --}}
    <style>
        :root { --bg: #f1f5f9; --card: #ffffff; --text: #1e293b; --muted: #64748b; --accent: #0d9488; }
        [data-theme="dark"] { --bg: #0c1222; --card: #1a2332; --text: #e2e8f0; --muted: #94a3b8; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: var(--card); border-radius: 14px; padding: 48px 40px; max-width: 460px; text-align: center; box-shadow: 0 10px 40px rgba(0,0,0,.08); }
        .code { font-size: 64px; font-weight: 800; color: var(--accent); line-height: 1; margin-bottom: 12px; }
        h1 { font-size: 20px; margin-bottom: 8px; }
        p { font-size: 14px; color: var(--muted); line-height: 22px; margin-bottom: 24px; }
        a { display: inline-block; background: var(--accent); color: #fff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 12px 28px; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="code">404</div>
        <h1>Page not found</h1>
        <p>The page you're looking for doesn't exist or may have been moved — perhaps the product was unpublished.</p>
        <a href="{{ route('home') }}">Back to the store</a>
    </div>
</body>
</html>
