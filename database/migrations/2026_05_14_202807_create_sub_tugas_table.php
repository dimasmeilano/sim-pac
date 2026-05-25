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
        Schema::create('sub_tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('progja_id')->constrained('program_kerja')->onDelete('cascade');
            $table->string('nama', 200);
            $table->foreignId('assignee_id')->nullable()->references('id')->on('users')->onDelete('set null');
            $table->enum('status', ['todo', 'progress', 'done', 'revisi'])->default('todo');
            $table->date('deadline')->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamps();

            $table->index('progja_id');
            $table->index('assignee_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_tugas');
    }
};
