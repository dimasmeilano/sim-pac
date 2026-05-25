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
        Schema::create('surat_masuk', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('nomor_surat', 100);
            $table->string('pengirim', 200);
            $table->string('perihal', 200);
            $table->text('isi_surat')->nullable();
            $table->string('lampiran')->nullable();
            $table->date('tanggal_surat');
            $table->date('tanggal_diterima');
            $table->enum('status', ['baru', 'diproses', 'selesai'])->default('baru');
            $table->text('disposisi')->nullable();
            $table->foreignId('diterima_oleh')->constrained('users');
            $table->timestamps();

            $table->index('nomor_surat');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surat_masuk');
    }
};
