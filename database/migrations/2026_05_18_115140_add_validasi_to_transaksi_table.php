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
        Schema::table('transaksi', function (Blueprint $table) {
            $table->enum('status_validasi', ['draft', 'menunggu', 'disetujui', 'ditolak'])->default('draft')->after('keterangan');
            $table->foreignId('divalidasi_oleh')->nullable()->after('created_by')->references('id')->on('users');
            $table->text('catatan_validasi')->nullable()->after('divalidasi_oleh');
            $table->datetime('tanggal_validasi')->nullable()->after('catatan_validasi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi', function (Blueprint $table) {
            $table->dropColumn(['status_validasi', 'divalidasi_oleh', 'catatan_validasi', 'tanggal_validasi']);
        });
    }
};
