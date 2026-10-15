@extends('layouts.app')

@section('title', 'Tambah foto')

@section('content')
    <header class="page-header form-page-header">
        <div>
            <a class="back-link" href="{{ route('photos.index') }}"><span aria-hidden="true">←</span> Kembali ke galeri</a>
            <p class="eyebrow">KOLEKSI PRIBADI <span>·</span> FOTO BARU</p>
            <h1>Tambah <em>momen.</em></h1>
            <p class="page-description">Simpan foto dan beri konteks agar ceritanya tak terlupa.</p>
        </div>
    </header>
    @include('photos.form', ['action' => route('photos.store'), 'method' => 'POST', 'submitLabel' => 'Simpan foto'])
@endsection