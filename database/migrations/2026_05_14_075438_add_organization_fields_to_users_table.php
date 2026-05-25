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
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom organization_id (foreign ke tabel organizations nanti)
            $table->foreignId('organization_id')->nullable()->after('remember_token');

            // Data pribadi anggota
            $table->string('nik', 16)->unique()->nullable()->after('organization_id');
            $table->string('tempat_lahir', 100)->nullable()->after('nik');
            $table->date('tanggal_lahir')->nullable()->after('tempat_lahir');
            $table->enum('jk', ['L', 'P'])->nullable()->after('tanggal_lahir');
            $table->string('no_hp', 15)->nullable()->after('jk');
            $table->string('pendidikan', 50)->nullable()->after('no_hp');

            // Media
            $table->string('foto')->nullable()->after('pendidikan');
            $table->string('qr_code')->nullable()->after('foto');

            // Status keanggotaan
            $table->enum('status_anggota', ['aktif', 'nonaktif', 'meninggal', 'keluar'])->default('aktif')->after('qr_code');
            $table->date('tgl_bergabung')->nullable()->after('status_anggota');
            $table->date('tgl_berhenti')->nullable()->after('tgl_bergabung');

            // Index untuk performa
            $table->index('organization_id');
            $table->index('nik');
            $table->index('no_hp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
