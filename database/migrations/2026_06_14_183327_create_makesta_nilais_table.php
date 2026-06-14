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
        Schema::create('makesta_nilais', function (Blueprint $table) {
            $table->id();

            // Relasi ke Materi apa yang dinilai
            $table->foreignId('makesta_materi_id')->constrained('makesta_materis')->onDelete('cascade');

            // Relasi ke Peserta siapa yang dinilai
            $table->foreignId('makesta_peserta_id')->constrained('makesta_pesertas')->onDelete('cascade');

            // Angka/Skor Penilaian (Misal 0 - 100)
            $table->integer('nilai')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makesta_nilais');
    }
};
