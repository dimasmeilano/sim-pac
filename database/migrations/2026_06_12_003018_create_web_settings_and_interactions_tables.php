<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. TABEL IDENTITAS WEB (Global PAC)
        Schema::create('identitas_webs', function (Blueprint $table) {
            $table->id();
            $table->string('nama_web')->default('SIM PAC IPNU IPPNU');
            $table->string('logo')->nullable();
            $table->text('deskripsi')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email')->nullable();
            $table->string('telepon')->nullable();
            $table->timestamps();
        });

        // 2. TABEL SLIDER BANNER (Global PAC)
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('gambar');
            $table->string('judul')->nullable();
            $table->string('deskripsi_singkat')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 3. TABEL TEKS BERJALAN / MARQUEE (Global PAC)
        Schema::create('teks_berjalans', function (Blueprint $table) {
            $table->id();
            $table->string('isi_teks');
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });

        // 4. TABEL PENGUNJUNG GLOBAL (Statistik Website)
        Schema::create('pengunjungs', function (Blueprint $table) {
            $table->id();
            $table->string('ip_address', 45); // Mendukung format IPv4 dan IPv6
            $table->date('tanggal');
            $table->integer('hits')->default(1);
            $table->timestamps();
        });

        // 5. TABEL KOMENTAR ARTIKEL (Langsung Tayang)
        Schema::create('komentars', function (Blueprint $table) {
            $table->id();
            // Relasi ke artikel: jika artikel dihapus, komentar ikut terhapus
            $table->foreignId('artikel_id')->constrained('artikels')->cascadeOnDelete();
            $table->string('nama_pengunjung');
            $table->string('email')->nullable(); // Opsional untuk pengunjung
            $table->text('isi_komentar');
            $table->timestamps();
        });

        // 6. TABEL MEDIA SOSIAL (Per Ranting / Organisasi)
        Schema::create('media_sosials', function (Blueprint $table) {
            $table->id();
            // Relasi ke ranting: siapa pemilik link medsos ini
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->string('nama_platform'); // Contoh: Instagram, TikTok, Facebook
            $table->string('url_link');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identitas_webs');
        Schema::dropIfExists('sliders');
        Schema::dropIfExists('teks_berjalans');
        Schema::dropIfExists('pengunjungs');
        Schema::dropIfExists('komentars');
        Schema::dropIfExists('media_sosials');
    }
};
