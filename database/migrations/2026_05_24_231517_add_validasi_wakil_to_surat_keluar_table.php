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
        Schema::table('surat_keluar', function (Blueprint $table) {
            // Status validasi
            $table->enum('status_validasi', [
                'draft',
                'menunggu_validasi_wakil',
                'menunggu_ttd_ketua',
                'menunggu_ttd_sekretaris',
                'selesai',
                'ditolak'
            ])->default('draft')->after('status');

            // Yang membuat (pengaju)
            $table->foreignId('diajukan_oleh')->nullable()->after('created_by')->references('id')->on('users');

            // Yang memvalidasi (wakil lain)
            $table->foreignId('divalidasi_oleh')->nullable()->after('diajukan_oleh')->references('id')->on('users');

            // Yang menandatangani
            $table->foreignId('ditandatangani_ketua_oleh')->nullable()->after('divalidasi_oleh')->references('id')->on('users');
            $table->foreignId('ditandatangani_sekretaris_oleh')->nullable()->after('ditandatangani_ketua_oleh')->references('id')->on('users');

            // Catatan dan tanggal
            $table->text('catatan_validasi')->nullable();
            $table->datetime('tanggal_validasi')->nullable();
            $table->datetime('tanggal_ttd_ketua')->nullable();
            $table->datetime('tanggal_ttd_sekretaris')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            //
        });
    }
};
