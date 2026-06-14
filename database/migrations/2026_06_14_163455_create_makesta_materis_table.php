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
        Schema::create('makesta_materis', function (Blueprint $table) {
            $table->id();

            // Relasi ke Event Makesta
            $table->foreignId('makesta_event_id')->constrained('makesta_events')->onDelete('cascade');

            // Info Materi & Instruktur
            $table->string('nama_materi'); // Contoh: Ke-NU-an, Ke-IPNU-an
            $table->string('nama_instruktur'); // Contoh: Rekan X dari PC
            $table->dateTime('waktu_materi')->nullable();

            // Magic Link & Keamanan (Rahasia Instruktur)
            $table->string('token_rahasia')->unique(); // URL unik (Contoh: aswaja-8f7A2)
            $table->string('pin_instruktur', 4); // PIN 4 digit untuk login (Contoh: 1926)

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('makesta_materis');
    }
};
