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
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->references('id')->on('organizations')->onDelete('set null');
            $table->foreignId('program_kerja_id')->nullable()->references('id')->on('program_kerja')->onDelete('set null');
            $table->string('nama', 200);
            $table->text('deskripsi')->nullable();
            $table->string('tempat', 255);
            $table->dateTime('tgl_mulai');
            $table->dateTime('tgl_selesai');
            $table->string('qr_code')->nullable();
            $table->enum('status', ['rencana', 'berlangsung', 'selesai', 'batal'])->default('rencana');
            $table->timestamps();

            $table->index('organization_id');
            $table->index('program_kerja_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kegiatan');
    }
};
