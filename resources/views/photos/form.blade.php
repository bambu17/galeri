<form class="photo-form" method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif
    <div class="form-fields">
        <div class="form-section-title"><span>01</span><div><h2>Detail foto</h2><p>Judul dan cerita membuat foto mudah ditemukan kembali.</p></div></div>
        <div class="form-field">
            <label for="title">Judul foto <span>*</span></label>
            <input id="title" name="title" type="text" maxlength="120" value="{{ old('title', $photo->title ?? '') }}" required placeholder="Contoh: Senja di ujung kota">
            @error('title')<span class="field-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-field">
            <label for="description">Cerita <span class="optional-label">OPSIONAL</span></label>
            <textarea id="description" name="description" rows="5" maxlength="2000" placeholder="Ceritakan tempat, waktu, atau hal kecil yang ingin Anda ingat...">{{ old('description', $photo->description ?? '') }}</textarea>
            @error('description')<span class="field-error">{{ $message }}</span>@enderror
        </div>
        <div class="form-field">
            <label for="image">{{ isset($photo) ? 'Ganti foto' : 'File foto' }} <span>*</span></label>
            @if (isset($photo))
                <div class="current-photo"><img src="{{ asset('storage/'.$photo->image_path) }}" alt="Foto saat ini: {{ $photo->title }}"><span>Foto saat ini</span></div>
            @endif
            <label class="upload-zone" for="image">
                <span class="upload-icon" aria-hidden="true">↑</span>
                <span class="upload-copy"><strong>Klik untuk memilih foto</strong><small>JPG, PNG, atau WebP · maksimal 5 MB{{ isset($photo) ? ' · kosongkan jika tidak ingin mengganti' : '' }}</small></span>
                <input id="image" name="image" type="file" accept="image/jpeg,image/png,image/webp" {{ isset($photo) ? '' : 'required' }}>
            </label>
            @error('image')<span class="field-error">{{ $message }}</span>@enderror
            <p class="upload-filename" data-upload-filename>Belum ada file dipilih</p>
        </div>
        <div class="form-actions"><a class="button button-quiet" href="{{ route('photos.index') }}">Batal</a><button class="button button-primary" type="submit">{{ $submitLabel }} <span aria-hidden="true">→</span></button></div>
    </div>
    <aside class="form-aside">
        <span class="aside-number">01 / 01</span>
        <div class="aside-mark" aria-hidden="true">rf</div>
        <p>“Simpan bukan sekadar gambarnya, tapi juga rasa saat mengambilnya.”</p>
        <span class="aside-rule"></span>
        <small>KOLEKSI PRIBADI<br>RUANG FOTO</small>
    </aside>
</form>
@push('scripts')
    <script>
        document.querySelector('#image')?.addEventListener('change', (event) => {
            document.querySelector('[data-upload-filename]').textContent = event.target.files[0]?.name ?? 'Belum ada file dipilih';
        });
    </script>
@endpush