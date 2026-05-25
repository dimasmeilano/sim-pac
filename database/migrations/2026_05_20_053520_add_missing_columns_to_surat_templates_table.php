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
            if (!Schema::hasColumn('surat_templates', 'klasifikasi')) {
                $table->string('klasifikasi', 50)->nullable()->after('kode');
            }
            if (!Schema::hasColumn('surat_templates', 'lampiran')) {
                $table->string('lampiran', 20)->nullable()->after('klasifikasi');
            }
            if (!Schema::hasColumn('surat_templates', 'jenis_surat')) {
                $table->enum('jenis_surat', ['keputusan', 'pengesahan', 'tugas', 'keterangan', 'undangan', 'umum'])->default('umum')->after('lampiran');
            }
            if (!Schema::hasColumn('surat_templates', 'fields')) {
                $table->json('fields')->nullable()->after('konten');
            }
            if (!Schema::hasColumn('surat_templates', 'urutan')) {
                $table->integer('urutan')->default(0)->after('fields');
            }
            if (!Schema::hasColumn('surat_templates', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('urutan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surat_templates', function (Blueprint $table) {
            $table->dropColumn(['klasifikasi', 'lampiran', 'jenis_surat', 'fields', 'urutan', 'is_active']);
        });
    }
};
