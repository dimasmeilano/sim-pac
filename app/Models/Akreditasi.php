<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Akreditasi extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $guarded = [];

    // Ubah data JSON dari database menjadi Array di dalam aplikasi
    // Ubah data JSON dari database menjadi Array di dalam aplikasi
    protected $casts = [
        // --- Casts untuk IPNU ---
        'bab1_keaswajaan'   => 'array',
        'bab2_pengkaderan'  => 'array',
        'bab3_instruktur'   => 'array',
        'bab4_pelajar_umum' => 'array',
        'bab6_sosial'       => 'array',
        'bab7_cbp'          => 'array',

        // --- Casts untuk IPPNU ---
        'ippnu_bab1_organisasi'  => 'array',
        'ippnu_bab2_kaderisasi'  => 'array',
        'ippnu_bab3_kelembagaan' => 'array',
        'ippnu_bab4_aswaja'      => 'array',
        'ippnu_bab5_kpp'         => 'array',
        'ippnu_bab6_media'       => 'array',
    ];

    // Relasi ke tabel Ranting/Organisasi
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Relasi ke Surat Permohonan
    public function suratPermohonan()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_permohonan_id');
    }

    // Relasi ke Surat Pernyataan
    public function suratPernyataan()
    {
        return $this->belongsTo(SuratKeluar::class, 'surat_pernyataan_id');
    }

    public function sekretaris()
    {
        // Tambahkan ->withoutGlobalScopes() agar Ranting bisa melihat nama PAC
        return $this->belongsTo(User::class, 'id_sekretaris')->withoutGlobalScopes();
    }

    public function ketua()
    {
        // Tambahkan ->withoutGlobalScopes() agar Ranting bisa melihat nama PAC
        return $this->belongsTo(User::class, 'id_ketua')->withoutGlobalScopes();
    }
}
