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
        Schema::create('lpjs', function (Blueprint $table) {
            $table->id();
            // Relasi ke Progja (1 LPJ hanya untuk 1 Progja)
            $table->foreignId('program_kerja_id')->constrained('program_kerja')->cascadeOnDelete();

            // Isian Singkat
            $table->string('tema_kegiatan')->nullable();
            $table->string('tempat_kegiatan')->nullable();
            $table->string('jam_kegiatan')->nullable();
            $table->string('nama_ketua_panitia')->nullable();
            $table->string('nama_sekretaris')->nullable();

            // Isian Teks Panjang (Textarea)
            $table->text('latar_belakang')->nullable();
            $table->text('dasar_pelaksanaan')->nullable();
            $table->text('tujuan_kegiatan')->nullable();
            $table->text('output_kegiatan')->nullable();
            $table->text('materi_kegiatan')->nullable();
            $table->text('hambatan_harapan')->nullable();

            // File Upload (Lampiran 2 & 3) disarankan format JPG/PNG agar bisa nempel di PDF
            $table->string('file_lampiran_panitia')->nullable();
            $table->string('file_lampiran_acara')->nullable();

            // Menyimpan maksimal 4 foto yang dicentang dari Galeri
            $table->json('foto_dokumentasi_terpilih')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lpjs');
    }
};
