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
        Schema::create('pengajuan_rekomendasis', function (Blueprint $table) {
            $table->id();

            // --- STEP 1: Data Organisasi ---
            $table->string('name');
            $table->string('type');
            $table->string('jenis_organisasi');
            $table->string('periode');
            $table->string('alamat')->nullable();
            $table->string('email_organisasi')->nullable();

            // --- STEP 2A: Data Akun Ketua ---
            $table->string('ketua_name');
            $table->string('ketua_email');
            $table->string('ketua_no_hp')->nullable();
            $table->enum('ketua_jk', ['Laki-laki', 'Perempuan'])->nullable();

            // --- STEP 2B: Data Akun Sekretaris ---
            $table->string('sekretaris_name');
            $table->string('sekretaris_email');
            $table->string('sekretaris_no_hp')->nullable();
            $table->enum('sekretaris_jk', ['Laki-laki', 'Perempuan'])->nullable();

            // --- STEP 3: Berkas Persyaratan (Standar Konbes IPNU-IPPNU) ---
            $table->string('file_surat_permohonan');
            $table->string('file_sk_konferensi');
            $table->string('file_ba_formatur');
            $table->string('file_sk_formatur');
            $table->string('file_susunan_pengurus');
            $table->string('file_rekomendasi_nu');
            $table->string('file_biodata_pengurus');
            $table->string('file_hasil_konferensi_lpj');
            $table->string('file_dokumentasi');
            $table->string('file_profil_organisasi');

            // --- SYSTEM: Status & Tracking ---
            $table->enum('status', ['menunggu_validasi', 'revisi', 'ditolak', 'disetujui'])->default('menunggu_validasi');
            $table->text('catatan_admin')->nullable();
            $table->unsignedBigInteger('divalidasi_oleh')->nullable();
            $table->timestamp('waktu_validasi')->nullable();

            $table->timestamps();

            $table->foreign('divalidasi_oleh')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengajuan_rekomendasis');
    }
};
