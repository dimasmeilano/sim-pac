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
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->enum('type', ['pac', 'ranting', 'departemen', 'lembaga'])->default('ranting');
            $table->foreignId('parent_id')->nullable()->references('id')->on('organizations')->onDelete('cascade');
            $table->text('alamat')->nullable();
            $table->string('kontak', 20)->nullable();
            $table->string('logo')->nullable();
            $table->string('kop_surat')->nullable();
            $table->string('stempel')->nullable();
            $table->string('pejabat_mengetahui', 100)->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('parent_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
