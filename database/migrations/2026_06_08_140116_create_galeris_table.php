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
        Schema::create('galeris', function (Blueprint $table) {
            $table->id();
            // Sekarang file menginduk ke Folder, bukan langsung ke Kegiatan
            $table->foreignId('workspace_folder_id')->constrained('workspace_folders')->cascadeOnDelete();

            $table->string('file_path');
            $table->string('nama_file');
            $table->string('keterangan')->nullable();

            // Nullable karena jika yang upload publik, sistem tidak tahu siapa usernya
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeris');
    }
};
