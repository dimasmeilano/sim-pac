<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'kegiatan';

    protected $fillable = [
        'organization_id',
        'program_kerja_id',
        'nama',
        'deskripsi',
        'tempat',
        'tgl_mulai',
        'tgl_selesai',
        'qr_code',
        'status',
        'ketua_pelaksana_id',
        'mode_absensi',
    ];

    protected $casts = [
        'tgl_mulai' => 'datetime',
        'tgl_selesai' => 'datetime',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function programKerja()
    {
        return $this->belongsTo(ProgramKerja::class, 'program_kerja_id');
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'kegiatan_id');
    }

    // Update status kegiatan berdasarkan waktu
    public function updateStatusByTime(): void
    {
        $now = now();

        if ($this->status == 'batal') {
            return; // Status batal tidak berubah otomatis
        }

        if ($now > $this->tgl_selesai) {
            $this->status = 'selesai';
        } elseif ($now >= $this->tgl_mulai && $now <= $this->tgl_selesai) {
            $this->status = 'berlangsung';
        } else {
            $this->status = 'rencana';
        }

        $this->saveQuietly(); // Simpan tanpa triggering event
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'rencana' => '<span class="badge badge-info">Rencana</span>',
            'berlangsung' => '<span class="badge badge-success">Berlangsung</span>',
            'selesai' => '<span class="badge badge-secondary">Selesai</span>',
            'batal' => '<span class="badge badge-danger">Batal</span>',
        ];
        return $statuses[$this->status] ?? '-';
    }
    public function ketuaPelaksana()
    {
        return $this->belongsTo(User::class, 'ketua_pelaksana_id');
    }
}
