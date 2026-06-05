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
        Schema::table('pengajuan_rekomendasis', function (Blueprint $table) {
            // Penanda apakah ini ranting baru atau perpanjangan
            $table->string('jenis_pengajuan')->default('baru')->after('id');

            // Mengaitkan pengajuan dengan organisasi yang sudah ada (jika ini perpanjangan)
            $table->unsignedBigInteger('organization_id')->nullable()->after('jenis_pengajuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengajuan_rekomendasis', function (Blueprint $table) {
            //
        });
    }
};
