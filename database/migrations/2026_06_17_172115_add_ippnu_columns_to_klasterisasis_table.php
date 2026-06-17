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
        Schema::table('klasterisasis', function (Blueprint $table) {
            // Penanda Identitas Organisasi
            $table->enum('jenis_organisasi', ['ipnu', 'ippnu'])->default('ipnu')->after('periode_penilaian');

            // Kolom Tambahan IPPNU untuk Parameter 1
            $table->json('p1_tabel_pimpinan')->nullable()->after('p1_link_bps');
            $table->float('p1_persentase_aktif')->nullable()->after('p1_tabel_pimpinan');

            // Kolom Tambahan IPPNU untuk Parameter 2
            $table->json('p2_tabel_proker')->nullable()->after('p2_tabel_pesantren');
            $table->float('p2_persentase_proker')->nullable()->after('p2_tabel_proker');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('klasterisasis', function (Blueprint $table) {
            //
        });
    }
};
