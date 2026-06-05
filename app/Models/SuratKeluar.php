<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class SuratKeluar extends Model
{
    use HasFactory, LogsActivity, BelongsToOrganization;

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
        'status_validasi',
        'diajukan_oleh',
        'divalidasi_oleh',
        'tanggal_validasi',
        'ditandatangani_sekretaris_oleh',
        'tanggal_ttd_sekretaris',
        'ditandatangani_ketua_oleh',
        'tanggal_ttd_ketua',

        // --- TAMBAHAN KOLOM BARU ---
        'tanggal_surat',
        'penerbit_surat',
        'acc_ipnu_at',
        'acc_ippnu_at',
        'acc_sekretaris_ipnu_at',
        'acc_sekretaris_ippnu_at',
    ];

    protected $casts = [
        'data_surat' => 'array',
        'tanggal_surat' => 'date', // Cast ke format Date
        'tanggal_ttd' => 'datetime',
        'tanggal_kirim' => 'datetime',
        'tanggal_validasi' => 'datetime',
        'tanggal_ttd_sekretaris' => 'datetime',
        'tanggal_ttd_ketua' => 'datetime',

        // --- TAMBAHAN CASTS TANGGAL BARU ---
        'acc_ipnu_at' => 'datetime',
        'acc_ippnu_at' => 'datetime',
        'acc_sekretaris_ipnu_at' => 'datetime',
        'acc_sekretaris_ippnu_at' => 'datetime',
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
            'menunggu_ttd_ketua' => '<span class="badge badge-primary">Menunggu Tanda Tangan Ketua</span>',
            'menunggu_ttd_sekretaris' => '<span class="badge badge-info">Menunggu Tanda Tangan Sekretaris</span>',
            'selesai' => '<span class="badge badge-success">Selesai & Sah</span>',
            'ditolak' => '<span class="badge badge-danger">Ditolak</span>',
        ];

        $status = $this->status_validasi ?? 'draft';

        return $statuses[$status] ?? '<span class="badge badge-dark">' . $status . '</span>';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Rekam semua perubahan kolom
            ->logOnlyDirty() // Hanya rekam kolom yang nilainya berubah
            ->setDescriptionForEvent(fn(string $eventName) => "Surat keluar telah di-{$eventName}");
    }
}
