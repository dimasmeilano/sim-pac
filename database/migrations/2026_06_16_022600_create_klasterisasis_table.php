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
        Schema::create('klasterisasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('periode_penilaian'); // Sangat bagus untuk membedakan data per tahun

            // ==========================================
            // A. Parameter Penduduk Muslim
            // ==========================================
            $table->enum('penduduk_muslim', ['0-19', '20-59', '60-100']);
            $table->integer('skor_penduduk')->default(0);
            // Bukti Portofolio:
            $table->string('p1_file_bukti')->nullable(); // Simpan path gambar screenshot BPS
            $table->string('p1_link_bps')->nullable();   // Simpan link URL BPS

            // ==========================================
            // B. Parameter Pesantren & Lembaga NU
            // ==========================================
            $table->enum('jumlah_pesantren', ['kurang_2', '2_sampai_3', 'lebih_3']);
            $table->integer('skor_pesantren')->default(0);
            // Bukti Portofolio:
            $table->json('p2_tabel_lembaga')->nullable();   // Simpan Array Nama & Alamat Sekolah
            $table->json('p2_tabel_pesantren')->nullable(); // Simpan Array Nama & Alamat Pesantren

            // ==========================================
            // C. Parameter Dukungan Stakeholder & Alumni
            // ==========================================
            $table->enum('dukungan_stakeholder', ['lemah', 'sedang', 'kuat']);
            $table->integer('skor_stakeholder')->default(0);
            // Bukti Portofolio:
            $table->json('p3_tabel_mou')->nullable();       // Simpan Array data MOU
            $table->json('p3_struktur_alumni')->nullable(); // Simpan Array Ketua, Sekretaris, Anggota
            $table->json('p3_kegiatan_alumni')->nullable(); // Simpan Array tabel pembinaan alumni

            // ==========================================
            // D. Parameter Kondisi Geografis
            // ==========================================
            $table->enum('kondisi_geografis', ['sulit', 'sedang', 'mudah']);
            $table->integer('skor_geografis')->default(0);
            // Bukti Portofolio:
            $table->string('p4_file_peta')->nullable();   // Simpan path gambar screenshot Maps
            $table->text('p4_infrastruktur')->nullable(); // Paragraf deskripsi kondisi jalan
            $table->text('p4_transportasi')->nullable();  // Paragraf deskripsi angkutan umum

            // ==========================================
            // Hasil Kalkulasi Akhir
            // ==========================================
            $table->integer('total_skor')->default(0);
            $table->integer('kluster')->nullable(); // Kluster 1, 2, atau 3

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('klasterisasis');
    }
};
