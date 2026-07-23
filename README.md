SIMMAG - Sistem Monitoring Magang Mahasiswa

SIMMAG adalah aplikasi berbasis web untuk mengelola dan memantau seluruh proses magang mahasiswa Fakultas Ilmu Komputer dalam satu platform terpusat.

Sistem ini membantu mahasiswa, dosen pembimbing, pembimbing lapangan, dan admin fakultas dalam mengelola Kerangka Acuan, logbook, bimbingan, laporan akhir, penilaian, notifikasi, arsip, serta riwayat aktivitas.

Informasi Proyek

Nama aplikasi: SIMMAG

Jenis aplikasi: Sistem monitoring magang berbasis web

Pengembang: Arini Winur Baeti

NIM: 20240803045

Program Studi: Sistem Informasi

Universitas: Universitas Esa Unggul

Tahun Akademik: Genap 2025/2026

Teknologi

PHP 8.3

Laravel 12

Livewire 3

Blade

Filament V3

MariaDB 10.11

Spatie Laravel Permission

Nginx

Docker dan Docker Compose

Fitur Utama

Autentikasi pengguna berdasarkan identitas resmi.

Pengelolaan role dan permission menggunakan RBAC.

Sinkronisasi data akademik dari SIAKAD.

Pengelolaan periode, instansi, dan penugasan magang.

Penyusunan dan persetujuan Kerangka Acuan.

Pengisian dan validasi logbook harian.

Pengajuan serta pencatatan bimbingan.

Pengunggahan dan review Laporan Akhir.

Penilaian lapangan dan penilaian akademik.

Dashboard monitoring berdasarkan role.

Notifikasi dan pengumuman.

Arsip digital dokumen mahasiswa.

Audit trail untuk mencatat perubahan penting.

Role Pengguna

Mahasiswa

Menyusun dan mengirim Kerangka Acuan.

Mengisi logbook harian.

Mengajukan bimbingan.

Mengunggah Laporan Akhir.

Melihat status dokumen dan hasil penilaian.

Dosen Pembimbing

Melakukan review akhir Kerangka Acuan.

Memantau logbook mahasiswa.

Mencatat hasil bimbingan.

Melakukan review Laporan Akhir.

Mengisi penilaian akademik.

Pembimbing Lapangan

Melakukan review awal Kerangka Acuan.

Memvalidasi atau meminta revisi logbook.

Mengisi penilaian lapangan.

Admin Fakultas

Mengelola pengguna, role, dan permission.

Mengelola data program studi, periode, dan instansi.

Mengelola data penugasan magang.

Menjalankan sinkronisasi SIAKAD.

Mengelola pengumuman, arsip, dan audit trail.

Memantau proses magang melalui Filament V3.

Persyaratan

Pastikan perangkat telah memiliki:

Docker Desktop atau Docker Engine

Docker Compose

Git

WSL untuk pengguna Windows

Untuk penggunaan boilerplate, pastikan custom command berikut telah aktif:

Command

Fungsi

dcu

Menjalankan Docker Compose

dcd

Menghentikan Docker Compose

dca

Menjalankan perintah Laravel Artisan

dcm

Membuat model, migration, seeder, controller, dan Filament Resource

Instalasi

1. Clone repository

git clone <URL_REPOSITORY>
cd simmag

2. Siapkan file environment

Apabila aplikasi Laravel berada di folder src, jalankan:

cp src/.env.example src/.env

Sesuaikan konfigurasi aplikasi dan database pada file .env berdasarkan layanan yang terdapat pada docker-compose.yml.

3. Jalankan container

dcu

4. Buat application key

dca key:generate

5. Jalankan migration dan seeder

dca migrate --seed

6. Buat symbolic link penyimpanan

dca storage:link

7. Bersihkan cache aplikasi

dca optimize:clear

Aplikasi dapat dibuka melalui alamat yang telah ditentukan pada konfigurasi Docker dan APP_URL.

Login

Pengguna dapat masuk menggunakan identitas berikut:

Mahasiswa: NIM

Dosen Pembimbing: NIDN

Admin Fakultas: NIP atau akun yang ditentukan sistem

Pembimbing Lapangan: identitas akun yang dibuat oleh admin

Password akun mengikuti data yang dibuat melalui seeder atau Admin Fakultas.

Setelah login, pengguna diarahkan ke dashboard sesuai role:

/dashboard/mahasiswa
/dashboard/dosen-pembimbing
/dashboard/pembimbing-lapangan
/dashboard/admin

Alur Utama Sistem

Kerangka Acuan

Draft -> Menunggu Review -> Disetujui PL -> Disetujui
                         -> Perlu Revisi

Pembimbing Lapangan melakukan review terlebih dahulu. Setelah disetujui, dokumen diteruskan kepada Dosen Pembimbing.

Logbook

Draft -> Menunggu Validasi -> Tervalidasi
                           -> Perlu Revisi

Validasi logbook hanya dilakukan oleh Pembimbing Lapangan. Dosen Pembimbing hanya memiliki akses monitoring.

Bimbingan

Diajukan -> Dijadwalkan -> Selesai
                       -> Dibatalkan

Laporan Akhir

Draft -> Menunggu Review -> Disetujui
                         -> Perlu Revisi

Status Magang

Status magang berubah menjadi Magang Selesai setelah penilaian lapangan dan penilaian akademik tersedia.

Struktur Direktori

simmag/
├── docker-compose.yml
├── nginx/
│   └── default.conf
└── src/
    ├── app/
    │   ├── Filament/
    │   ├── Http/
    │   │   ├── Controllers/
    │   │   └── Middleware/
    │   ├── Livewire/
    │   ├── Models/
    │   ├── Policies/
    │   ├── Services/
    │   └── Support/
    ├── database/
    │   ├── migrations/
    │   └── seeders/
    ├── resources/
    │   ├── views/
    │   ├── css/
    │   └── js/
    ├── routes/
    │   └── web.php
    ├── public/
    │   └── images/
    ├── storage/
    │   └── app/public/
    └── tests/

Pengembangan

Membuat modul Laravel dan Filament

Gunakan custom command proyek:

dcm NamaModul

Command tersebut digunakan untuk membuat model, migration, seeder, controller, dan Filament Resource sesuai konfigurasi boilerplate.

Menjalankan perintah Artisan

Gunakan dca sebagai pengganti php artisan.

Contoh:

dca make:middleware EnsureSimmagRole
dca make:policy LogbookPolicy --model=Logbook
dca migrate
dca db:seed

Menjalankan pengujian

dca test

Reset database pengembangan

Perintah berikut menghapus seluruh data pada database.

dca migrate:fresh --seed

Menghentikan container

dcd

Aturan Bisnis Penting

Kerangka Acuan direview secara berurutan oleh Pembimbing Lapangan dan Dosen Pembimbing.

Validasi logbook hanya dilakukan oleh Pembimbing Lapangan.

Dosen Pembimbing dapat memantau logbook tanpa mengubah status validasi.

Logbook dan bimbingan tidak memiliki input durasi.

Persetujuan tidak menggunakan tanda tangan digital.

Persetujuan dicatat melalui identitas pengguna, waktu, perubahan status, dan audit trail.

Penilaian lapangan dan akademik disimpan secara terpisah.

Sistem tidak menghitung nilai akhir gabungan secara otomatis.

Ukuran maksimal unggahan berkas adalah 20 MB.

Keamanan

Password disimpan menggunakan hashing Laravel.

Hak akses dibatasi menggunakan role, permission, middleware, dan policy.

Sistem menerapkan perlindungan CSRF dan validasi input.

Sesi berakhir setelah 30 menit tidak aktif.

Lima kegagalan login dalam lima menit menyebabkan akun dikunci sementara selama 30 menit.

Perubahan status penting disimpan pada audit trail.

Troubleshooting

Perubahan konfigurasi belum diterapkan

dca optimize:clear

File unggahan tidak dapat diakses

dca storage:link

Pastikan folder storage dan bootstrap/cache memiliki izin tulis.

Database belum memiliki tabel

dca migrate --seed

Akses halaman menghasilkan 403

Periksa role, permission, middleware, dan policy pengguna terkait.

Pengujian

Pengujian sistem mencakup:

Login dan pengalihan dashboard berdasarkan role.

Pembatasan akses antarpengguna.

Alur Kerangka Acuan.

Validasi logbook.

Pengelolaan bimbingan.

Review Laporan Akhir.

Penilaian lapangan dan akademik.

Perubahan status magang.

Audit trail.

User Acceptance Testing.

Catatan

SIMMAG dikembangkan sebagai Capstone Project Program Studi Sistem Informasi Universitas Esa Unggul. Proyek ini digunakan untuk kepentingan akademik dan pengembangan sistem monitoring magang mahasiswa.