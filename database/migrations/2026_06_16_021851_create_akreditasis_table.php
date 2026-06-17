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
        Schema::create('akreditasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');

            // ==========================================
            // LAPIS 1: IDENTITAS & SURAT (Serba Otomatis)
            // ==========================================
            // Menautkan 2 ID Surat dari tabel surat_keluars
            $table->unsignedBigInteger('surat_permohonan_id')->nullable();
            $table->unsignedBigInteger('surat_pernyataan_id')->nullable();

            $table->text('kata_pengantar')->nullable();
            $table->text('deskripsi_singkat')->nullable();

            // ==========================================
            // LAPIS 2: BAB V (Khusus Administrasi Ranting)
            // ==========================================
            $table->string('bab5_no_sp')->nullable(); // Nomor Surat Pengesahan
            $table->string('bab5_file_ba')->nullable(); // Upload PDF Berita Acara Konferensi

            // ==========================================
            // LAPIS 2: DATA DINAMIS (Disimpan sebagai Array/JSON)
            // ==========================================
            // Kolom ini akan menampung form repeater (baris yang bisa ditambah terus oleh ranting)
            $table->json('bab1_keaswajaan')->nullable();
            $table->json('bab2_pengkaderan')->nullable();
            $table->json('bab3_instruktur')->nullable();
            $table->json('bab4_pelajar_umum')->nullable();
            $table->json('bab6_sosial')->nullable();
            $table->json('bab7_cbp')->nullable();

            // ==========================================
            // LAPIS 3: STATUS & PENILAIAN PAC
            // ==========================================
            $table->string('status')->default('Draft'); // Draft, Menunggu PAC, Selesai
            $table->string('grade_akhir')->nullable(); // A/B/C/D
            $table->text('catatan_pac')->nullable();
            $table->timestamp('dinilai_pada')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('akreditasis');
    }
};
