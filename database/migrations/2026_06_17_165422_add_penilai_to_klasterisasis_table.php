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
            // Kolom Verifikasi Berlapis
            $table->string('status')->default('Menunggu Review Sekretaris');
            $table->unsignedBigInteger('id_sekretaris')->nullable();
            $table->text('catatan_sekretaris')->nullable();
            $table->unsignedBigInteger('id_ketua')->nullable();
            $table->text('catatan_ketua')->nullable();
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
