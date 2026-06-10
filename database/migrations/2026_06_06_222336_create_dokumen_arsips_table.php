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
        Schema::create('dokumen_arsips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Siapa yang upload

            // e-library (Modul/Buku) atau repository (SK, Proposal, Surat)
            $table->enum('kategori', ['e-library', 'repository']);

            $table->string('nama_dokumen');
            $table->text('deskripsi')->nullable();

            // Detail File
            $table->string('file_path');
            $table->string('tipe_file', 10); // pdf, docx, xlsx, dll
            $table->integer('ukuran_file')->nullable(); // dalam KB

            // Kunci Gembok Akses
            $table->enum('hak_akses', ['publik', 'internal', 'rahasia'])->default('internal');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumen_arsips');
    }
};
