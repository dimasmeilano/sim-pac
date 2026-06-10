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
        Schema::create('artikels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategori_artikels')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete(); // Menyimpan data siapa yang posting

            $table->string('judul');
            $table->string('slug')->unique();
            $table->string('gambar_cover')->nullable();
            $table->longText('isi_artikel');

            // Kolom Credit Jurnalistik
            $table->string('kontributor')->nullable();
            $table->string('editor')->nullable();
            $table->string('fotografer')->nullable();

            $table->integer('dilihat')->default(0); // Menghitung jumlah klik/viewer
            $table->enum('status', ['draft', 'publish'])->default('draft');
            $table->timestamp('published_at')->nullable(); // Tanggal dan jam tayang
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikels');
    }
};
