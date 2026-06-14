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
        Schema::table('identitas_webs', function (Blueprint $table) {
            $table->text('sejarah_singkat')->nullable();
            $table->text('visi_misi')->nullable();
            $table->text('makna_lambang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('identitas_webs', function (Blueprint $table) {
            //
        });
    }
};
