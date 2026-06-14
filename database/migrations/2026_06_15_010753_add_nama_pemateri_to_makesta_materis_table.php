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
        Schema::table('makesta_materis', function (Blueprint $table) {
            $table->string('nama_pemateri')->nullable()->after('nama_materi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('makesta_materis', function (Blueprint $table) {
            //
        });
    }
};
