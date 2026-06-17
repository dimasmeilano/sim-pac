# SIM PAC - Sistem Informasi Manajemen Pimpinan Anak Cabang

SIM PAC adalah aplikasi berbasis web yang dirancang khusus untuk mempermudah tata kelola administrasi, keuangan, dan pengawasan Ranting pada tingkat Pimpinan Anak Cabang (PAC). Aplikasi ini mengusung hierarki akses yang jelas antara Super Admin, Pengurus PAC, dan Pengurus Ranting.

## 🚀 Fitur Utama

- **Dasbor Komprehensif:** Pemantauan statistik organisasi secara _real-time_ (Keuangan, Surat, dan Keaktifan Ranting) menggunakan visualisasi Chart.js.
- **Manajemen Hierarki & Role:** Hak akses berbasis _role_ menggunakan Spatie (Super Admin, Ketua PAC, Sekretaris PAC, Bendahara PAC, Ketua Ranting, dll).
- **Sistem Administrasi Persuratan:** Tracking dan validasi surat menyurat secara digital.
- **Manajemen Kas & Keuangan:** Pemisahan pencatatan arus kas (Kas IPNU, Kas IPPNU, dan Kas Bersama).
- **Klasterisasi & Akreditasi Ranting:** Penilaian otomatis dan pemetaan kualitas ranting/komisariat (Utama, Sedang, Binaan) beserta predikat akreditasinya.
- **Pusat Operasional Organisasi:** Dilengkapi dengan pendataan anggota, pemantauan program kerja, papan Kanban, dan ruang diskusi internal.
- **Peringatan Masa Aktif SK:** Indikator otomatis untuk ranting yang berstatus Kritis atau Demisioner.

## 🛠️ Tech Stack

- **Framework:** [Laravel](https://laravel.com/)
- **Frontend Template:** [AdminLTE 3](https://adminlte.io/) (Bootstrap 4)
- **Database:** MySQL
- **Visualisasi Data:** [Chart.js](https://www.chartjs.org/)
- **Role Management:** Spatie Laravel Permission

## ⚙️ Cara Instalasi (Local Development)

Ikuti langkah-langkah di bawah ini untuk menjalankan project di _localhost_:

1. **Clone repository ini:**

````bash
   git clone [https://github.com/username-anda/sim-pac.git](https://github.com/username-anda/sim-pac.git)
   cd sim-pac
Install dependensi Composer:

Bash
   composer install
Install dependensi NPM (opsional jika ada custom asset):

Bash
   npm install
   npm run dev
Konfigurasi Environment:

Duplikat file .env.example menjadi .env.

Buka file .env dan atur konfigurasi database Anda:

Cuplikan kode
     DB_CONNECTION=mysql
     DB_HOST=127.0.0.1
     DB_PORT=3306
     DB_DATABASE=nama_database_anda
     DB_USERNAME=root
     DB_PASSWORD=
     ```

5. **Generate Application Key:**
```bash
   php artisan key:generate
Migrasi Database & Seeder:
(Pastikan Anda membuat database kosong terlebih dahulu di MySQL/phpMyAdmin)

Bash
   php artisan migrate --seed
Jalankan Aplikasi:

Bash
   php artisan serve
Aplikasi dapat diakses melalui http://localhost:8000.

🚧 Roadmap / Pengembangan Mendatang
[ ] Integrasi Google Calendar untuk sinkronisasi jadwal kegiatan otomatis.

[ ] Integrasi Google Meet API untuk generate link Meeting Room.

[ ] Export otomatis Laporan Pertanggungjawaban (LPJ) PDF/Excel.

🤝 Kontribusi
Aplikasi ini dikembangkan untuk kemajuan tata kelola organisasi. Kritik, saran, maupun Pull Request sangat diterima!

📄 Lisensi
Proyek ini berada di bawah lisensi MIT License.
````
