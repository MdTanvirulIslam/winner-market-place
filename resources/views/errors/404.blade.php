<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page Not Found — {{ config('app.name') }}</title>
    <script>
        try {
            // Matches the storefront: dark unless the visitor chose light.
            if (localStorage.getItem('winnerTAM-dark') !== 'false') {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        } catch (e) {}
    </script>
    {{-- Standalone styles: error pages must render even if Vite assets fail. --}}
    <style>
        :root { --bg: #f6f7fb; --card: #ffffff; --card-border: #e4e6f0; --text: #1e293b; --muted: #64748b; --accent: #6366f1; --accent2: #a855f7; }
        [data-theme="dark"] { --bg: #0a0a14; --card: #12121f; --card-border: rgba(255,255,255,.09); --text: #f4f4f8; --muted: #9aa0b5; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
        .card { background: var(--card); border: 1px solid var(--card-border); border-radius: 18px; padding: 48px 40px; max-width: 460px; text-align: center; box-shadow: 0 18px 60px rgba(0,0,0,.18); }
        .code { font-size: 64px; font-weight: 800; line-height: 1; margin-bottom: 12px; background: linear-gradient(135deg, var(--accent), var(--accent2)); -webkit-background-clip: text; background-clip: text; color: transparent; }
        h1 { font-size: 20px; margin-bottom: 8px; }
        p { font-size: 14px; color: var(--muted); line-height: 22px; margin-bottom: 24px; }
        a { display: inline-block; background: linear-gradient(135deg, var(--accent), var(--accent2)); color: #fff; text-decoration: none; font-size: 14px; font-weight: 700; padding: 12px 28px; border-radius: 12px; box-shadow: 0 8px 26px rgba(99,102,241,.35); }
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
