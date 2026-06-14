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
        Schema::table('makesta_events', function (Blueprint $table) {
            $table->string('biaya')->nullable()->after('kuota_peserta'); // Contoh: Rp 30.000 / Gratis
            $table->string('contact_person')->nullable()->after('biaya'); // Contoh: 08123456789 (Rekan Fulan)
            $table->text('fasilitas')->nullable()->after('contact_person');
            $table->text('persyaratan')->nullable()->after('fasilitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('makesta_events', function (Blueprint $table) {
            //
        });
    }
};
