<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('artikels', function (Blueprint $table) {
            // 1. Tambahkan kolom catatan_editor
            // Kita pakai pengecekan agar tidak error jika kolom sudah terlanjur terbuat saat percobaan gagal tadi
            if (!Schema::hasColumn('artikels', 'catatan_editor')) {
                $table->text('catatan_editor')->nullable();
            }
        });

        // SINTAKS KHUSUS POSTGRESQL

        // 2. Hapus aturan (constraint) ENUM lama yang dibuat Laravel
        DB::statement('ALTER TABLE artikels DROP CONSTRAINT IF EXISTS artikels_status_check');

        // 3. Ubah tipe kolom menjadi VARCHAR biasa (Jauh lebih aman dan fleksibel di Postgres)
        DB::statement('ALTER TABLE artikels ALTER COLUMN status TYPE VARCHAR(255)');

        // 4. Set nilai default-nya kembali ke 'draft'
        DB::statement("ALTER TABLE artikels ALTER COLUMN status SET DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artikels', function (Blueprint $table) {
            //
        });
    }
};
