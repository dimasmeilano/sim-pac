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
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->references('id')->on('organizations')->onDelete('set null');
            $table->foreignId('program_kerja_id')->nullable()->references('id')->on('program_kerja')->onDelete('set null');
            $table->foreignId('kegiatan_id')->nullable()->references('id')->on('kegiatan')->onDelete('set null');
            $table->string('kode_transaksi')->unique();
            $table->string('judul', 200);
            $table->enum('jenis', ['masuk', 'keluar']);
            $table->decimal('nominal', 15, 2);
            $table->date('tanggal');
            $table->string('kategori', 50)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti_file')->nullable();
            $table->foreignId('created_by')->nullable()->references('id')->on('users');
            $table->timestamps();

            $table->index('organization_id');
            $table->index('program_kerja_id');
            $table->index('jenis');
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
