<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratKeluar extends Model
{
    use HasFactory;

    protected $table = 'surat_keluar';

    protected $fillable = [
        'organization_id',
        'template_id',
        'jenis_surat',
        'nomor_surat',
        'perihal',
        'tujuan',
        'isi_surat',
        'data_surat',
        'lampiran',
        'status',
        'created_by',
        'ditandatangani_oleh',
        'tanggal_ttd',
        'tanggal_kirim',
        'status_validasi',   // <-- PASTIKAN INI ADA!
        'diajukan_oleh',
        'divalidasi_oleh',
        'diajukan_oleh',
        'divalidasi_oleh',
        'tanggal_validasi',
        'ditandatangani_sekretaris_oleh', // <-- PASTIKAN INI ADA
        'tanggal_ttd_sekretaris',         // <-- PASTIKAN INI ADA
        'ditandatangani_ketua_oleh',      // <-- PASTIKAN INI ADA
        'tanggal_ttd_ketua',
    ];

    protected $casts = [
        'data_surat' => 'array',
        'tanggal_ttd' => 'datetime',
        'tanggal_kirim' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function template()
    {
        return $this->belongsTo(SuratTemplate::class, 'template_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'ditandatangani_oleh');
    }

    public function diajukanOleh()
    {
        return $this->belongsTo(User::class, 'diajukan_oleh');
    }

    public function divalidasiOleh()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }

    public function ditandatanganiKetuaOleh()
    {
        return $this->belongsTo(User::class, 'ditandatangani_ketua_oleh');
    }

    public function ditandatanganiSekretarisOleh()
    {
        return $this->belongsTo(User::class, 'ditandatangani_sekretaris_oleh');
    }

    public function getStatusValidasiTextAttribute()
    {
        $statuses = [
            'draft' => '<span class="badge badge-secondary">Draft</span>',
            'menunggu_validasi_wakil' => '<span class="badge badge-warning">Menunggu Validasi Wakil</span>',
            'menunggu_ttd_ketua' => '<span class="badge badge-warning">Menunggu Tanda Tangan Ketua</span>',
            'menunggu_ttd_sekretaris' => '<span class="badge badge-warning">Menunggu Tanda Tangan Sekretaris</span>',
            'selesai' => '<span class="badge badge-success">Selesai</span>',
            'ditolak' => '<span class="badge badge-danger">Ditolak</span>',
        ];

        $status = $this->status_validasi ?? 'draft';

        return $statuses[$status] ?? '<span class="badge badge-secondary">' . $status . '</span>';
    }
}
