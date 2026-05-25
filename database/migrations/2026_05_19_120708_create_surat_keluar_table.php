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
        Schema::create('surat_keluar', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->foreignId('template_id')->nullable()->constrained('surat_templates')->onDelete('set null');
            $table->string('nomor_surat', 100)->unique();
            $table->string('perihal', 200);
            $table->text('tujuan');
            $table->text('isi_surat');
            $table->string('lampiran')->nullable();
            $table->enum('status', ['draft', 'menunggu_ttd', 'selesai', 'ditolak'])->default('draft');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('ditandatangani_oleh')->nullable()->constrained('users');
            $table->datetime('tanggal_ttd')->nullable();
            $table->datetime('tanggal_kirim')->nullable();
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
        Schema::dropIfExists('surat_keluar');
    }
};
