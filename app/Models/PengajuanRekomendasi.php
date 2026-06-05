<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengajuanRekomendasi extends Model
{
    use HasFactory;

    protected $table = 'pengajuan_rekomendasis';

    protected $fillable = [
        'jenis_pengajuan',   // <-- Tambahkan ini
        'organization_id',
        'name',
        'type',
        'jenis_organisasi',
        'periode',
        'alamat',
        'email_organisasi',
        'ketua_name',
        'ketua_email',
        'ketua_no_hp',
        'ketua_jk',
        'sekretaris_name',
        'sekretaris_email',
        'sekretaris_no_hp',
        'sekretaris_jk',
        // 10 Berkas Sesuai Konbes
        'file_surat_permohonan',
        'file_sk_konferensi',
        'file_ba_formatur',
        'file_sk_formatur',
        'file_susunan_pengurus',
        'file_rekomendasi_nu',
        'file_biodata_pengurus',
        'file_hasil_konferensi_lpj',
        'file_dokumentasi',
        'file_profil_organisasi',
        // Sistem
        'status',
        'catatan_admin',
        'divalidasi_oleh',
        'waktu_validasi',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }
}
