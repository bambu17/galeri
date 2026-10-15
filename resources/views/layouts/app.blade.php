<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Ruang Foto') · Ruang Foto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar">
            <a class="brand" href="{{ route('photos.index') }}" aria-label="Ruang Foto beranda">
                <span class="brand-mark" aria-hidden="true">rf</span>
                <span>ruang<span class="brand-light">foto</span></span>
            </a>
            <div class="sidebar-label">RUANG KERJA</div>
            <a class="nav-link {{ request()->routeIs('photos.index') ? 'is-active' : '' }}" href="{{ route('photos.index') }}">
                <span class="nav-icon" aria-hidden="true">▦</span> Galeri saya
                <span class="nav-count">{{ auth()->user()->photos()->count() }}</span>
            </a>
            <a class="nav-link {{ request()->routeIs('photos.create') ? 'is-active' : '' }}" href="{{ route('photos.create') }}">
                <span class="nav-icon" aria-hidden="true">＋</span> Tambah foto
            </a>
            <div class="sidebar-bottom">
                <div class="profile-chip">
                    <span class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="profile-copy"><strong>{{ auth()->user()->name }}</strong><small>{{ auth()->user()->email }}</small></span>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="signout-button" type="submit"><span aria-hidden="true">↗</span> Keluar</button>
                </form>
                <p class="sidebar-note">Koleksi pribadi, tersimpan aman.</p>
            </div>
        </aside>

        <main class="main-content">
            @if (session('status'))
                <div class="flash-message" role="status">{{ session('status') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
    @stack('scripts')
</body>
</html>