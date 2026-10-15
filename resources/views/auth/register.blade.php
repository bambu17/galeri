@extends('layouts.guest')

@section('title', 'Buat akun')

@section('content')
    <div class="auth-eyebrow"><span></span> MULAI KOLEKSI ANDA</div>
    <h1>Galeri pribadi,<br><em>cerita Anda.</em></h1>
    <p class="auth-intro">Buat akun untuk menyimpan dan merawat momen terbaik.</p>
    <form class="auth-form" method="POST" action="{{ route('register.store') }}">
        @csrf
        <label for="name">Nama lengkap</label>
        <input id="name" name="name" type="text" value="{{ old('name') }}" autocomplete="name" required autofocus placeholder="Nama Anda">
        @error('name')<span class="field-error">{{ $message }}</span>@enderror
        <label for="email">Alamat email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required placeholder="nama@email.com">
        @error('email')<span class="field-error">{{ $message }}</span>@enderror
        <label for="password">Kata sandi</label>
        <input id="password" name="password" type="password" autocomplete="new-password" required placeholder="Min. 8 karakter">
        @error('password')<span class="field-error">{{ $message }}</span>@enderror
        <label for="password_confirmation">Ulangi kata sandi</label>
        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" required placeholder="Ketik ulang kata sandi">
        <button class="button button-primary auth-submit" type="submit">Buat akun <span aria-hidden="true">→</span></button>
    </form>
    <p class="auth-switch">Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
@endsection