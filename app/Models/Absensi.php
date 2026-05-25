<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';
    protected $fillable = [
        'kegiatan_id',
        'user_id',
        'nama_peserta',
        'asal_peserta',
        'no_hp_peserta',
        'waktu_absen',
        'status',
        'keterangan',
        'ip_address'

    ];

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
}
