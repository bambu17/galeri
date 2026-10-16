@extends('layouts.admin')

@section('title', 'Akun pengguna')

@section('content')
    <header class="page-header admin-page-header">
        <div>
            <p class="eyebrow">PENGELOLAAN AKUN</p>
            <h1>Akun <em>pengguna.</em></h1>
            <p class="page-description">Daftar nama akun dan email pengguna terdaftar. Menghapus akun juga menghapus posting dan foto pengguna.</p>
        </div>
        <a class="button button-quiet" href="{{ route('admin.dashboard') }}">Kembali ke moderasi</a>
    </header>

    <section class="admin-activity">
        <div class="admin-activity-heading">
            <div>
                <h2>Daftar akun</h2>
                <p>{{ $users->total() }} pengguna terdaftar.</p>
            </div>
            <form class="search-form" method="GET" action="{{ route('admin.users') }}">
                <label class="search-input">
                    <span aria-hidden="true">⌕</span>
                    <input type="search" name="search" value="{{ $search }}" placeholder="Cari nama atau email..." aria-label="Cari nama atau email">
                </label>
            </form>
        </div>

        @if ($users->isNotEmpty())
            <div class="admin-table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th scope="col">Nama / username</th>
                            <th scope="col">Email</th>
                            <th scope="col">Tanggal bergabung</th>
                            <th scope="col">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($users as $user)
                            <tr>
                                <td><strong>{{ $user->name }}</strong></td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->translatedFormat('d M Y, H:i') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Hapus akun ini beserta seluruh posting dan foto secara permanen?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="admin-action-button is-danger" type="submit">Hapus akun</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pagination-wrap">{{ $users->links() }}</div>
        @else
            <section class="empty-state compact-empty admin-empty">
                <div class="empty-symbol" aria-hidden="true">⌕</div>
                <h2>{{ $search !== '' ? 'Tidak ada hasil.' : 'Belum ada pengguna.' }}</h2>
                <p>{{ $search !== '' ? 'Coba kata kunci lain.' : 'Akun pengguna yang terdaftar akan muncul di sini.' }}</p>
            </section>
        @endif
    </section>
@endsection
