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
        Schema::create('makesta_pesertas', function (Blueprint $table) {
            $table->id();

            // Relasi ke Event Makesta yang didaftar
            $table->foreignId('makesta_event_id')->constrained('makesta_events')->onDelete('cascade');

            // Data Pribadi Peserta
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tgl_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('no_wa');
            $table->text('alamat');

            // Asal Delegasi / Utusan (Misal: PR Desa A, PK SMP B)
            $table->string('utusan');

            // Bukti Persyaratan (Upload Surat Rekomendasi / Pas Foto / Bukti Transfer)
            $table->string('berkas_syarat')->nullable();

            // Status Peserta selama Makesta (Menunggu, Mengikuti, Lulus, Tidak Lulus)
            $table->string('status_kelulusan')->default('Menunggu');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makesta_pesertas');
    }
};
