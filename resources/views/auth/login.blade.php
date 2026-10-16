@extends('layouts.guest')

@section('title', 'Masuk')

@section('content')
    <div class="auth-eyebrow"><span></span> GALERI FOTO PRIBADI</div>
    <h1>Semua momen,<br><em>satu tempat.</em></h1>
    <p class="auth-intro">Masuk untuk melanjutkan cerita di galeri Anda.</p>
    <form class="auth-form" method="POST" action="{{ route('login.store') }}">
        @csrf
        <label for="email">Alamat email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="nama@email.com">
        @error('email')<span class="field-error">{{ $message }}</span>@enderror
        <div class="field-heading"><label for="password">Kata sandi</label><span>Minimal 8 karakter</span></div>
        <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Masukkan kata sandi">
        @error('password')<span class="field-error">{{ $message }}</span>@enderror
        <label class="check-row"><input type="checkbox" name="remember" value="1"> <span>Ingat saya di perangkat ini</span></label>
        <button class="button button-primary auth-submit" type="submit">Masuk ke galeri <span aria-hidden="true">→</span></button>
    </form>
    <p class="auth-switch">Belum punya akun? <a href="{{ route('register') }}">Buat akun</a></p>
    <p class="auth-switch">Administrator? <a href="{{ route('admin.login') }}">Masuk ke panel admin</a></p>
@endsection