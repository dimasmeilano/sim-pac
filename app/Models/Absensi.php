<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Absensi extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'absensi';

    protected $guarded = [];

    protected $casts = [
        'waktu_absen' => 'datetime',
    ];

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'hadir' => '<span class="badge badge-success">Hadir</span>',
            'izin' => '<span class="badge badge-warning">Izin</span>',
            'sakit' => '<span class="badge badge-info">Sakit</span>',
            'alpha' => '<span class="badge badge-danger">Alpha</span>',
        ];
        return $statuses[$this->status] ?? '-';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Rekam semua kolom
            ->logOnlyDirty() // Hanya rekam kolom yang nilainya berubah (saat diedit)
            ->dontSubmitEmptyLogs() // Jangan rekam kalau tidak ada perubahan
            ->setDescriptionForEvent(fn(string $eventName) => "Data Absensi telah di-{$eventName}");
    }
}
