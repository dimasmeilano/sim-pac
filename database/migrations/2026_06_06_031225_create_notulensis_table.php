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
        Schema::create('notulensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->foreignId('kegiatan_id')->nullable()->constrained('kegiatan')->onDelete('set null'); // Opsional jika nyambung ke acara
            $table->string('agenda'); // Topik/Judul Rapat
            $table->date('tanggal');
            $table->time('waktu_mulai')->nullable();
            $table->time('waktu_selesai')->nullable();
            $table->string('tempat');
            $table->string('pemimpin_rapat'); // Bisa teks manual, karena kadang dipimpin oleh senior/alumni
            $table->foreignId('notulis_id')->constrained('users')->onDelete('cascade'); // Yang mengetik notulensi
            $table->longText('pembahasan');
            $table->longText('kesimpulan')->nullable();
            $table->enum('status', ['draft', 'final'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notulensi');
    }
};
