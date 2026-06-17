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

        Schema::create('alumnis', function (Blueprint $table) {
            $table->id();
            // Relasi ke Ranting/PAC tempat ia bernaung dulu
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');

            $table->string('nama_lengkap');
            $table->enum('jenis_organisasi', ['ipnu', 'ippnu']); // Membedakan MA IPNU dan Alumni IPPNU
            $table->string('no_hp', 20)->nullable();
            $table->string('email')->nullable();

            // Riwayat di Organisasi
            $table->string('tahun_angkatan', 4)->nullable(); // Tahun ikut Makesta/Bergabung
            $table->string('jabatan_terakhir')->nullable(); // Misal: Ketua PAC 2020-2022

            // Networking & Karir (Sangat penting untuk koneksi)
            $table->string('profesi')->nullable(); // Misal: Pengusaha, Guru, Dosen, PNS
            $table->string('instansi_pekerjaan')->nullable(); // Misal: Kemenag Gresik, PT Petrokimia
            $table->string('alamat_domisili')->nullable();

            $table->boolean('bersedia_menjadi_donatur')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumnis');
    }
};
