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
        Schema::table('program_kerja', function (Blueprint $table) {
            // Tambahkan kolom estimasi anggaran (bigInteger agar muat triliunan)
            $table->bigInteger('estimasi_anggaran')->default(0)->after('deskripsi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_kerjas', function (Blueprint $table) {
            //
        });
    }
};
