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
            $table->dropColumn('makna_lambang');

            // Tambahkan 2 kolom yang baru
            $table->text('makna_lambang_ipnu')->nullable();
            $table->text('makna_lambang_ippnu')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('identitas_webs', function (Blueprint $table) {
            $table->dropColumn(['makna_lambang_ipnu', 'makna_lambang_ippnu']);
            $table->text('makna_lambang')->nullable();
        });
    }
};
