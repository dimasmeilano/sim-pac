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
        Schema::table('akreditasis', function (Blueprint $table) {
            $table->unsignedBigInteger('id_sekretaris')->nullable(); // Siapa yang mereview
            $table->unsignedBigInteger('id_ketua')->nullable();     // Siapa yang mengesahkan
            $table->text('catatan_sekretaris')->nullable();         // Catatan verifikasi teknis
            // Mengubah default kolom yang sudah ada
            $table->string('status')->default('Menunggu Review Sekretaris')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('akreditasis', function (Blueprint $table) {
            //
        });
    }
};
