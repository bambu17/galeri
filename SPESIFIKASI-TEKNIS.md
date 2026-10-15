# Spesifikasi Teknis Aplikasi Galeri Foto

## Kebutuhan Perangkat

| Perangkat | Spesifikasi minimum | Kegunaan |
| --- | --- | --- |
| Laptop/PC pengembangan | Prosesor 4-core, RAM 8 GB, ruang kosong 10 GB, layar 1366 x 768 | Menulis kode, menjalankan server lokal, dan menguji aplikasi |
| Server aplikasi/database | 2 vCPU, RAM 4 GB, ruang awal 20 GB; tambah kapasitas sesuai jumlah foto dan cadangan | Menjalankan Laravel, MySQL, serta menyimpan berkas foto |
| Perangkat client | Komputer atau ponsel dengan browser modern dan koneksi jaringan | Membuka galeri melalui jaringan/internet |
| Perangkat input foto | Kamera ponsel/kamera digital atau berkas foto yang sudah tersedia | Menyiapkan bahan untuk diunggah |
| Jaringan | LAN atau internet yang stabil; HTTPS untuk penggunaan publik | Menghubungkan browser client ke server |

Spesifikasi server di atas adalah titik awal pengembangan/penggunaan ringan. Kapasitas produksi perlu disesuaikan dengan jumlah pengguna, ukuran foto, trafik, serta kebijakan pencadangan.

## Perangkat Lunak

| Software | Versi/kebutuhan | Peran |
| --- | --- | --- |
| Visual Studio Code | Versi stabil | Editor kode; tersedia pada lingkungan pengembangan ini |
| PHP | 8.3 atau lebih baru pada seri yang didukung proyek | Menjalankan Laravel |
| Composer | 2.x | Mengelola paket PHP |
| Laravel | 13.x | Routing, autentikasi, validasi, ORM, dan server aplikasi |
| MySQL | 8.0+ pada server; SQLite dapat digunakan untuk tes | Menyimpan data akun dan metadata foto |
| Node.js dan npm | Node.js 22.12+ atau 24 LTS | Memasang paket dan membangun aset Vite/Tailwind |
| Browser | Chrome, Edge, Firefox, atau Safari versi modern | Menjalankan antarmuka client |
| Laravel Pint / PHPUnit | Versi yang terkunci di Composer | Format dan pengujian kode |

Ekstensi VS Code yang disarankan: PHP Intelephense dan Laravel Blade support. Proyek saat ini tidak menambah ekstensi wajib pada aplikasi.

## Interpretasi Program

- Bentuk aplikasi: web client-server. Browser mengirim permintaan HTTP ke server Laravel; Laravel memvalidasi permintaan, membaca/menulis MySQL, dan menyimpan berkas pada disk publik.
- Autentikasi menggunakan sesi Laravel. Password disimpan dalam bentuk hash, bukan teks biasa.
- Setiap foto terhubung ke satu pengguna. Pengguna hanya dapat melihat, menambah, mengedit, atau menghapus fotonya sendiri.
- Login, registrasi, logout, pencarian judul/deskripsi, daftar galeri, upload, edit metadata/gambar, dan hapus foto disediakan.
- Format upload: JPG/JPEG, PNG, dan WebP; ukuran maksimum 5 MB per foto.
- Tidak ada peran administrator pada spesifikasi saat ini; semua akun terdaftar memiliki hak yang sama atas koleksi masing-masing.

## Pembagian Hak Akses

| Fitur | Tamu | Pengguna masuk |
| --- | :---: | :---: |
| Registrasi | Ya | Tidak diperlukan |
| Login | Ya | Tidak diperlukan |
| Lihat galeri sendiri | Tidak | Ya |
| Tambah foto | Tidak | Ya |
| Edit foto sendiri | Tidak | Ya |
| Hapus foto sendiri | Tidak | Ya |
| Logout | Tidak | Ya |
| Akses foto milik akun lain | Tidak | Tidak |

## Struktur Database

- `users`: nama, email unik, hash password, token sesi, dan timestamp.
- `photos`: `user_id` sebagai foreign key ke `users`, judul, deskripsi opsional, path berkas foto, dan timestamp.
- Relasi `users` ke `photos` adalah satu-ke-banyak. Penghapusan akun menghapus metadata foto terkait melalui cascade; berkas fisik mengikuti pengelolaan storage aplikasi.

## Menjalankan Aplikasi

1. Atur koneksi database pada `.env` dan pastikan MySQL aktif.
2. Jalankan `php artisan migrate` untuk membuat tabel.
3. Jalankan `php artisan storage:link` agar gambar pada disk publik dapat ditampilkan.
4. Jalankan `npm install` lalu `npm run build` untuk aset antarmuka.
5. Jalankan `php artisan serve` untuk pengujian lokal, atau gunakan virtual host Laragon.