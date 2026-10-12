<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Welcome') }} - {{ config('app.name', 'Laravel') }}</title>
    <link rel="stylesheet" href="{{ asset('css/fonts.css') }}?v={{ filemtime(public_path('css/fonts.css')) }}">
    <style>
        * { box-sizing: border-box; }
        body { min-height: 100vh; margin: 0; display: flex; align-items: center; justify-content: center; background: #fff; color: #333; font-family: "Sarabun", sans-serif; }
        main { width: min(680px, calc(100% - 40px)); padding: 64px 24px; text-align: center; }
        h1 { margin: 0 0 12px; font-size: clamp(42px, 8vw, 72px); font-weight: 500; letter-spacing: -3px; }
        p { color: #777; line-height: 1.7; }
        nav { display: flex; justify-content: center; flex-wrap: wrap; gap: 18px; margin-top: 30px; }
        a { color: #e34b32; font-size: 14px; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <main>
        <h1>Laravel</h1>
        <p>Your application is ready. Start building your next idea.</p>
        <nav>
            <a href="https://laravel.com/docs" target="_blank" rel="noopener">Documentation</a>
            <a href="https://laracasts.com" target="_blank" rel="noopener">Laracasts</a>
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}">Log in</a>
                @endauth
            @endif
        </nav>
    </main>
</body>
</html>
