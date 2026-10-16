<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Panel admin') · Ruang Foto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="admin-shell">
        <header class="admin-topbar">
            <a class="brand" href="{{ route('admin.dashboard') }}" aria-label="Ruang Foto panel admin">
                <span class="brand-mark" aria-hidden="true">rf</span>
                <span>ruang<span class="brand-light">foto</span></span>
            </a>
            <div class="admin-topbar-actions">
                <a class="button button-quiet" href="{{ route('admin.users') }}">Akun pengguna</a>
                <span class="admin-label">PANEL ADMIN</span>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="button button-quiet" type="submit">Keluar</button>
                </form>
            </div>
        </header>
        <main class="admin-content">
            @if (session('status'))
                <div class="flash-message" role="status">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>
