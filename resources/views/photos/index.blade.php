@extends('layouts.app')

@section('title', 'Galeri saya')

@section('content')
    <header class="page-header">
        <div>
            <p class="eyebrow">KOLEKSI PRIBADI <span>·</span> {{ now()->translatedFormat('d F Y') }}</p>
            <h1>Galeri <em>saya.</em></h1>
            <p class="page-description">Momen yang Anda simpan, tersusun rapi di satu tempat.</p>
        </div>
        <a class="button button-primary" href="{{ route('photos.create') }}"><span aria-hidden="true">＋</span> Tambah foto</a>
    </header>

    <section class="gallery-toolbar" aria-label="Kontrol galeri">
        <div class="gallery-total"><strong>{{ $photoCount }}</strong> foto tersimpan</div>
        <form class="search-form" method="GET" action="{{ route('photos.index') }}">
            <label class="search-input"><span aria-hidden="true">⌕</span><input type="search" name="search" value="{{ $search }}" placeholder="Cari judul atau cerita..." aria-label="Cari foto"></label>
            @if ($search !== '')<a class="clear-search" href="{{ route('photos.index') }}">Hapus pencarian</a>@endif
        </form>
    </section>

    @if ($photos->isNotEmpty())
        <section class="photo-grid" aria-label="Foto dalam galeri">
            @foreach ($photos as $photo)
                <article class="photo-card">
                    <a class="photo-image-link" href="{{ route('photos.edit', $photo) }}" aria-label="Lihat dan edit {{ $photo->title }}">
                        <img class="photo-image" src="{{ asset('storage/'.$photo->image_path) }}" alt="{{ $photo->title }}" loading="lazy">
                    </a>
                    <div class="photo-meta">
                        <div class="photo-copy"><h2>{{ $photo->title }}</h2><p>{{ $photo->description ?: 'Belum ada cerita untuk foto ini.' }}</p></div>
                        <a class="icon-button" href="{{ route('photos.edit', $photo) }}" aria-label="Edit {{ $photo->title }}" title="Edit foto">↗</a>
                    </div>
                    <div class="photo-date">DITAMBAHKAN {{ $photo->created_at->translatedFormat('d M Y') }}</div>
                </article>
            @endforeach
        </section>
        <div class="pagination-wrap">{{ $photos->links() }}</div>
    @elseif ($search !== '')
        <section class="empty-state compact-empty">
            <div class="empty-symbol" aria-hidden="true">⌕</div>
            <h2>Belum ada hasil.</h2>
            <p>Coba kata lain atau hapus pencarian untuk melihat semua foto.</p>
            <a class="text-link" href="{{ route('photos.index') }}">Lihat semua foto <span aria-hidden="true">→</span></a>
        </section>
    @else
        <section class="empty-state">
            <div class="empty-still-life" aria-hidden="true"><span></span><span></span><span></span></div>
            <p class="eyebrow">HALAMAN PERTAMA</p>
            <h2>Setiap koleksi<br>dimulai dari <em>satu foto.</em></h2>
            <p>Unggah foto pertama dan tambahkan cerita kecil di baliknya.</p>
            <a class="button button-primary" href="{{ route('photos.create') }}"><span aria-hidden="true">＋</span> Tambah foto pertama</a>
        </section>
    @endif
@endsection