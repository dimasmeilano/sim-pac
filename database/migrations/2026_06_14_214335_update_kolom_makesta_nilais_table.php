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
        Schema::table('makesta_nilais', function (Blueprint $table) {
            $table->dropColumn('nilai');

            // Tambahkan kolom-kolom baru sesuai gambar
            $table->integer('kognitif')->nullable();
            $table->integer('keaktifan')->nullable();
            $table->integer('nilai_akhir')->nullable();
            $table->string('abjad', 5)->nullable();
            $table->text('catatan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('makesta_nilais', function (Blueprint $table) {
            //
        });
    }
};
