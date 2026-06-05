<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            // Menambahkan relasi ke tabel organizations. Posisinya setelah kolom tujuan yang lama.
            $table->foreignId('tujuan_organization_id')
                ->nullable() // Boleh kosong jika suratnya untuk pihak eksternal
                ->constrained('organizations')
                ->onDelete('set null')
                ->after('tujuan'); // Ganti 'tujuan' dengan nama kolom string Anda saat ini
        });
    }

    public function down()
    {
        Schema::table('surat_keluar', function (Blueprint $table) {
            $table->dropForeign(['tujuan_organization_id']);
            $table->dropColumn('tujuan_organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
};
