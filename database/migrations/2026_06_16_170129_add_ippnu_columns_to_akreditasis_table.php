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
            // Suntikkan kolom penanda IPNU/IPPNU tepat setelah organization_id
            $table->enum('jenis_borang', ['ipnu', 'ippnu'])->default('ipnu')->after('organization_id');

            // Suntikkan kolom JSON khusus IPPNU
            $table->json('ippnu_bab1_organisasi')->nullable();
            $table->json('ippnu_bab2_kaderisasi')->nullable();
            $table->json('ippnu_bab3_kelembagaan')->nullable();
            $table->json('ippnu_bab4_aswaja')->nullable();
            $table->json('ippnu_bab5_kpp')->nullable();
            $table->json('ippnu_bab6_media')->nullable();
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
