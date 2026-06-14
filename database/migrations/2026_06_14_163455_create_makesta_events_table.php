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
        Schema::create('makesta_events', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel organisasi (Untuk mengetahui siapa penyelenggaranya: PAC atau Ranting A/B)
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');

            // Informasi Dasar Event
            $table->string('tema');
            $table->string('lokasi');
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->integer('kuota_peserta')->nullable();

            // Berkas Pengajuan (Khusus untuk Ranting yang mengajukan ke PAC)
            $table->string('berkas_proposal')->nullable();

            // Status Event (Menunggu Verifikasi, Disetujui, Berjalan, Selesai)
            $table->string('status')->default('Menunggu Verifikasi');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makesta_events');
    }
};
