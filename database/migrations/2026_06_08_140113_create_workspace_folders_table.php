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
        Schema::create('workspace_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();

            $table->string('nama_folder'); // Contoh: "Dokumentasi", "Kuitansi Rahasia"
            $table->text('deskripsi')->nullable();

            // Pengaturan Privasi
            $table->enum('tipe_akses', ['private', 'public'])->default('private');
            $table->boolean('izinkan_upload_publik')->default(false); // KUNCI: Orang luar bisa ikut upload atau tidak
            $table->string('share_token')->nullable()->unique();

            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workspace_folders');
    }
};
