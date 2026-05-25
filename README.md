# Sistem Informasi Administrasi Surat (SIAS) - PAC IPNU IPPNU

Sistem Informasi Administrasi Surat (SIAS) adalah aplikasi berbasis web yang dibangun menggunakan framework **Laravel** untuk mengotomatisasi pencatatan, pengarsipan, validasi berjenjang, dan pencetakan surat menyurat pada Pimpinan Anak Cabang (PAC) IPNU IPPNU.

Aplikasi ini dilengkapi dengan fitur pembuatan surat menggunakan template dinamis, alur validasi silang (cross-check), tanda tangan digital otomatis berbasis Base64, serta penggabungan file lampiran langsung ke dalam file PDF akhir.

---

## 🚀 Fitur Utama

### 1. Manajemen Surat Keluar (Template Dinamis)

- Pembuatan surat otomatis menggunakan sistem placeholder penahan aman berbasis kurung siku (`[TTD_KETUA]`, `[TTD_SEKRETARIS]`, `[STEMPEL]`) untuk menghindari pemotongan otomatis oleh Rich Text Editor.
- Penerjemahan otomatis variabel organisasi (contoh: `{jenis_organisasi_upper}`, `{nama_organisasi_lower}`) sesuai dengan hak akses organisasi user yang sedang login.

### 2. Alur Validasi Berjenjang & Tanda Tangan Digital (Cross-Check Workflow)

Sistem menerapkan alur birokrasi organisasi secara ketat dan aman:

1.  **Draft:** Surat dibuat oleh pembuat surat.
2.  **Menunggu Validasi Wakil:** Surat diajukan kepada Wakil Ketua lain yang ditunjuk untuk validasi silang (_cross-check_).
3.  **Menunggu TTD Sekretaris:** Setelah disetujui Wakil, Sekretaris PAC memberikan Tanda Tangan Digital melalui menu profil mereka.
4.  **Menunggu TTD Ketua:** Surat berlanjut ke Ketua PAC untuk penandatanganan akhir dan penyematan stempel organisasi secara otomatis.
5.  **Selesai:** Surat resmi sah, tanda tangan & stempel otomatis muncul di halaman detail (`show`) dan siap diunduh menjadi PDF.

### 3. Ekspor PDF & Merger Lampiran Otomatis

- Menggunakan **DomPDF** untuk merender struktur HTML surat menjadi PDF secara presisi.
- Menggunakan **FPDI** untuk menggabungkan dokumen utama dengan file lampiran (baik berupa gambar `JPG`/`PNG` maupun file `PDF` tambahan) ke halaman belakang dokumen secara otomatis tanpa merusak tata letak surat.
- Gambar Tanda Tangan dan Stempel di-render menggunakan _Stream Base64 Encoding_ langsung dari direktori penyimpanan internal (`storage_path`) untuk memastikan asset gambar tidak pernah gagal dimuat (_broken link_).

### 4. Manajemen Surat Masuk & Disposisi

- Pencatatan surat masuk beserta tanggal diterima dan file fisik lampiran.
- Fitur **Disposisi Surat** oleh Ketua PAC kepada pengurus terkait untuk ditindaklanjuti secara sistematis.

---

## 🛠️ Tech Stack

- **Framework Utama:** Laravel (PHP)
- **Database:** MySQL / MariaDB
- **PDF Generator:** `barryvdh/laravel-dompdf` (DomPDF)
- **PDF Merger / Importer:** `setasign/fpdi`
- **Template Engine:** Blade Templating
- **Autentikasi & Hak Akses:** Spatie Laravel-permission (Roles: `super_admin`, `ketua_pac`, `sekretaris_pac`, `wakil`)

---

## ⚙️ Cara Instalasi & Konfigurasi

### 1. Kloning Repositori

```bash
git clone [https://github.com/username/sias-ipnu-ippnu.git](https://github.com/username/sias-ipnu-ippnu.git)
cd sias-ipnu-ippnu
2. Instalasi Dependensi PHP
Bash
composer install
3. Konfigurasi Environment File
Salin file .env.example menjadi .env dan sesuaikan pengaturan database Anda:

Bash
cp .env.example .env
Lalu, sesuaikan bagian konfigurasi database di file .env:

Cuplikan kode
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=root
DB_PASSWORD=
4. Generate Application Key & Jalankan Migrasi
Bash
php artisan key:generate
php artisan migrate --seed
(Catatan: --seed digunakan jika Anda memiliki data dummy/awal untuk Role, Permission, dan Akun)

5. Membuat Symbolic Link Storage
Sistem menyimpan file aset tanda tangan, stempel, dan lampiran di dalam direktori storage/app/public. Jalankan perintah ini agar aset dapat diakses dengan baik:

Bash
php artisan storage:link
6. Jalankan Server Lokal
Bash
php artisan serve
Buka http://127.0.0.1:8000 pada browser Anda.

📝 Catatan Penting untuk Pengembang
Format Placeholder TTD: Jangan pernah mengubah simbol tanda tangan dari kurung siku [TTD_SEKRETARIS] menjadi kurung kurawal {ttd_sekretaris} di master template HTML, karena kurung kurawal kosong rentan dihapus secara otomatis oleh sistem sanitasi Rich Text Editor (CKEditor/TinyMCE).

Izin Direktori Temp: Sistem memerlukan folder temporary untuk memproses penggabungan dokumen PDF. Pastikan aplikasi memiliki izin menulis pada folder storage/app/temp/. (Sistem sudah dilengkapi auto-create folder dengan hak izin 0775).

Made with ❤️ oleh PAC IPNU IPPNU.
```
