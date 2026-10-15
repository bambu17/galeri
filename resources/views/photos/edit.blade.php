@extends('layouts.app')

@section('title', 'Edit foto')

@section('content')
    <header class="page-header form-page-header">
        <div>
            <a class="back-link" href="{{ route('photos.index') }}"><span aria-hidden="true">←</span> Kembali ke galeri</a>
            <p class="eyebrow">KOLEKSI PRIBADI <span>·</span> EDIT FOTO</p>
            <h1>Atur <em>cerita.</em></h1>
            <p class="page-description">Perbarui detail atau ganti gambar foto ini.</p>
        </div>
    </header>
    @include('photos.form', ['photo' => $photo, 'action' => route('photos.update', $photo), 'method' => 'PUT', 'submitLabel' => 'Simpan perubahan'])
    <form class="delete-form" method="POST" action="{{ route('photos.destroy', $photo) }}" onsubmit="return confirm('Hapus foto ini dari galeri? Tindakan ini tidak dapat dibatalkan.')">
        @csrf
        @method('DELETE')
        <button class="delete-button" type="submit"><span aria-hidden="true">×</span> Hapus foto</button>
    </form>
@endsection