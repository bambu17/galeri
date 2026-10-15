<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') · Ruang Foto</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Playfair+Display:wght@500;600;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
</head>
<body class="auth-page">
    <div class="auth-shell">
        <section class="auth-panel">
            <a class="brand auth-brand" href="{{ route('login') }}">
                <span class="brand-mark" aria-hidden="true">rf</span><span>ruang<span class="brand-light">foto</span></span>
            </a>
            <div class="auth-content">
                @yield('content')
            </div>
            <p class="auth-legal">Dengan melanjutkan, foto Anda hanya dapat diakses oleh akun Anda.</p>
        </section>
        <aside class="auth-art" aria-label="Pratinjau suasana galeri foto">
            <div class="art-orbit art-orbit-one"></div><div class="art-orbit art-orbit-two"></div>
            <div class="art-caption"><span>01 / MOMEN YANG BERARTI</span><p>“Yang kita simpan,<br>tetap hidup lebih lama.”</p></div>
            <div class="art-stamp">RF<br><small>EST. 2026</small></div>
        </aside>
    </div>
    @stack('scripts')
</body>
</html>