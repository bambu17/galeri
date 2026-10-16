@extends('layouts.guest')

@section('title', 'Masuk admin')

@section('content')
    <div class="auth-eyebrow"><span></span> AKSES ADMINISTRATOR</div>
    <h1>Kelola galeri,<br><em>jaga ruangnya.</em></h1>
    <p class="auth-intro">Masuk untuk meninjau aktivitas dan memoderasi posting pengguna.</p>
    <form class="auth-form" method="POST" action="{{ route('admin.login.store') }}">
        @csrf
        <label for="username">Username admin</label>
        <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required autofocus placeholder="Masukkan username">
        @error('username')<span class="field-error">{{ $message }}</span>@enderror
        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required placeholder="Masukkan kata sandi">
        @error('password')<span class="field-error">{{ $message }}</span>@enderror
        <button class="button button-primary auth-submit" type="submit">Masuk ke panel admin <span aria-hidden="true">→</span></button>
    </form>
    <p class="auth-switch"><a href="{{ route('login') }}">Kembali ke login pengguna</a></p>
@endsection
