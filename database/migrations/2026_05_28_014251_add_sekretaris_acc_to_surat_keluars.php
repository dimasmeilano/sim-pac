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
            // Tambahkan kolom untuk mencatat ACC Sekretaris IPNU & IPPNU
            $table->dateTime('acc_sekretaris_ipnu_at')->nullable()->after('tanggal_ttd_sekretaris');
            $table->dateTime('acc_sekretaris_ippnu_at')->nullable()->after('acc_sekretaris_ipnu_at');

            // Kita anggap acc_ipnu_at dan acc_ippnu_at yang sudah ada 
            // sebagai ACC Ketua IPNU dan Ketua IPPNU.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_keluars', function (Blueprint $table) {
            //
        });
    }
};
