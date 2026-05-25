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
        Schema::create('program_kerja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->references('id')->on('organizations')->onDelete('set null');
            $table->string('nama', 200);
            $table->text('deskripsi')->nullable();
            $table->date('tgl_mulai');
            $table->date('tgl_selesai');
            $table->enum('status', ['planning', 'active', 'completed', 'cancelled'])->default('planning');
            $table->timestamps();

            $table->index('organization_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_kerja');
    }
};
