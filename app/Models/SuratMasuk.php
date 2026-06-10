<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SuratMasuk extends Model
{
    use HasFactory, BelongsToOrganization, LogsActivity;

    protected $table = 'surat_masuk';

    protected $fillable = [
        'organization_id',
        'nomor_surat',
        'pengirim',
        'perihal',
        'isi_surat',
        'lampiran',
        'tanggal_surat',
        'tanggal_diterima',
        'status',
        'disposisi',
        'diterima_oleh'
    ];

    protected $casts = [
        'tanggal_surat' => 'date',
        'tanggal_diterima' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'baru' => '<span class="badge badge-primary">Baru</span>',
            'diproses' => '<span class="badge badge-warning">Diproses</span>',
            'selesai' => '<span class="badge badge-success">Selesai</span>',
        ];
        return $statuses[$this->status] ?? '-';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Rekam semua kolom
            ->logOnlyDirty() // Hanya rekam kolom yang nilainya berubah (saat diedit)
            ->dontSubmitEmptyLogs() // Jangan rekam kalau tidak ada perubahan
            ->setDescriptionForEvent(fn(string $eventName) => "Data Surat Masuk telah di-{$eventName}");
    }
}
