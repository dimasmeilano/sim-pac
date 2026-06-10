<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ProgramKerja extends Model
{
    use HasFactory, BelongsToOrganization, LogsActivity;

    protected $table = 'program_kerja';

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    protected $guarded = [];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function tugas()
    {
        return $this->hasMany(SubTugas::class, 'progja_id')->orderBy('urutan');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'progja_id');
    }

    // Relasi ke bawah: 1 Progja punya BANYAK Kegiatan
    public function kegiatans()
    {
        // Sesuaikan 'Kegiatan::class' dengan nama Model Kegiatan Anda
        return $this->hasMany(Kegiatan::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class, 'program_kerja_id');
    }

    public function lpj()
    {
        return $this->hasOne(Lpj::class, 'program_kerja_id');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'planning' => 'Perencanaan',
            'active' => 'Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getJenisTextAttribute()
    {
        $jenis = [
            'ipnu' => 'IPNU',
            'ippnu' => 'IPPNU',
            'bersama' => 'Bersama',
        ];
        return $jenis[$this->jenis] ?? ucfirst($this->jenis);
    }

    public function repliedMessage()
    {
        return $this->belongsTo(Message::class, 'reply_to_id'); // Ganti Message::class dengan nama Model Anda jika berbeda
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Rekam semua kolom
            ->logOnlyDirty() // Hanya rekam kolom yang nilainya berubah (saat diedit)
            ->dontSubmitEmptyLogs() // Jangan rekam kalau tidak ada perubahan
            ->setDescriptionForEvent(fn(string $eventName) => "Data Program Kerja telah di-{$eventName}");
    }
}
