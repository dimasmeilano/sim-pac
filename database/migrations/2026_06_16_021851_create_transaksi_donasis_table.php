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
        Schema::create('transaksi_donasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_donasi_id')->constrained('campaign_donasis')->onDelete('cascade');

            // Bisa berelasi ke Alumni, bisa juga Null jika donatur dari luar/anonim
            $table->foreignId('alumni_id')->nullable()->constrained('alumnis')->onDelete('set null');

            $table->string('nama_donatur')->nullable(); // Diisi jika bukan alumni
            $table->decimal('nominal', 15, 2);
            $table->enum('metode_pembayaran', ['transfer_bank', 'tunai', 'qris']);
            $table->string('bukti_transfer')->nullable();
            $table->text('pesan_harapan')->nullable(); // Doa/Pesan dari donatur

            // Status verifikasi oleh Bendahara
            $table->enum('status_pembayaran', ['pending', 'verified', 'rejected'])->default('pending');
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_donasis');
    }
};
