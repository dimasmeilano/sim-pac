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
        Schema::table('artikels', function (Blueprint $table) {
            // Kita buat nullable() dulu agar data artikel percobaan sebelumnya tidak membuat PostgreSQL error
            $table->unsignedBigInteger('organization_id')->nullable()->after('id');

            // Relasikan ke tabel organizations
            $table->foreign('organization_id')->references('id')->on('organizations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artikels', function (Blueprint $table) {
            //
        });
    }
};
