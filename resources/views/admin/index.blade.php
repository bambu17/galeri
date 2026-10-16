@extends('layouts.admin')

@section('title', 'Aktivitas user')

@section('content')
    <header class="page-header admin-page-header">
        <div>
            <p class="eyebrow">MODERASI <span>·</span> {{ now()->translatedFormat('d F Y') }}</p>
            <h1>Aktivitas <em>user.</em></h1>
            <p class="page-description">Tinjau posting terbaru dan lakukan moderasi pada galeri.</p>
        </div>
    </header>

    <section class="admin-stats" aria-label="Ringkasan aktivitas">
        <article class="admin-stat"><span>Total posting</span><strong>{{ $photoCount }}</strong></article>
        <article class="admin-stat"><span>Posting diblokir</span><strong>{{ $blockedPhotoCount }}</strong></article>
        <article class="admin-stat"><span>Pengguna terdaftar</span><strong>{{ $userCount }}</strong></article>
    </section>

    <section class="admin-activity">
        <div class="admin-activity-heading">
            <div>
                <h2>Posting pengguna</h2>
                <p>Diurutkan berdasarkan aktivitas terakhir.</p>
            </div>
            <form class="search-form" method="GET" action="{{ route('admin.dashboard') }}">
                <label class="search-input"><span aria-hidden="true">⌕</span><input type="search" name="search" value="{{ $search }}" placeholder="Cari posting atau pengguna..." aria-label="Cari posting atau pengguna"></label>
            </form>
        </div>

        @if ($photos->isNotEmpty())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th scope="col">Posting</th>
                            <th scope="col">Pengguna</th>
                            <th scope="col">Aktivitas</th>
                            <th scope="col">Status</th>
                            <th scope="col">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($photos as $photo)
                            <tr>
                                <td>
                                    <div class="admin-post">
                                        <img src="{{ asset('storage/'.$photo->image_path) }}" alt="" loading="lazy">
                                        <div><strong>{{ $photo->title }}</strong><small>{{ $photo->description ?: 'Tanpa deskripsi' }}</small></div>
                                    </div>
                                </td>
                                <td><strong>{{ $photo->user->name }}</strong><small class="admin-user-email">{{ $photo->user->email }}</small></td>
                                <td><span class="admin-activity-date">Dibuat {{ $photo->created_at->translatedFormat('d M Y, H:i') }}</span><span class="admin-activity-date">Diperbarui {{ $photo->updated_at->translatedFormat('d M Y, H:i') }}</span></td>
                                <td><span class="admin-status {{ $photo->is_blocked ? 'is-blocked' : 'is-active' }}">{{ $photo->is_blocked ? 'Diblokir' : 'Aktif' }}</span></td>
                                <td>
                                    <div class="admin-actions">
                                        <form method="POST" action="{{ route('admin.photos.status', $photo) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="is_blocked" value="{{ $photo->is_blocked ? '0' : '1' }}">
                                            <button class="admin-action-button" type="submit">{{ $photo->is_blocked ? 'Aktifkan' : 'Blokir' }}</button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.photos.destroy', $photo) }}" onsubmit="return confirm('Hapus posting dan file foto ini secara permanen?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-action-button is-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">{{ $photos->links() }}</div>
        @else
            <section class="empty-state compact-empty admin-empty">
                <div class="empty-symbol" aria-hidden="true">⌕</div>
                <h2>{{ $search !== '' ? 'Tidak ada hasil.' : 'Belum ada posting.' }}</h2>
                <p>{{ $search !== '' ? 'Coba kata kunci lain.' : 'Posting terbaru pengguna akan muncul di sini.' }}</p>
            </section>
        @endif
    </section>
@endsection
