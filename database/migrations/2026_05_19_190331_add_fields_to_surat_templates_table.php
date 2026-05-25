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
        Schema::table('surat_templates', function (Blueprint $table) {
            $table->json('fields')->nullable()->after('konten');
            $table->string('klasifikasi', 50)->nullable()->after('kode');
            $table->string('lampiran', 20)->nullable()->after('klasifikasi');
            $table->enum('jenis_surat', ['keputusan', 'pengesahan', 'tugas', 'keterangan', 'undangan', 'umum'])->default('umum')->after('jenis');
            $table->integer('urutan')->default(0)->after('fields');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_templates', function (Blueprint $table) {
            //
        });
    }
};
