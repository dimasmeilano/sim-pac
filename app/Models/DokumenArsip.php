<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DokumenArsip extends Model
{
    use HasFactory, LogsActivity;

    // Membuka izin untuk semua kolom agar bisa diisi secara massal
    protected $guarded = [];

    // ==========================================
    // PENGATURAN CCTV (AUDIT TRAIL)
    // ==========================================
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded() // Rekam semua kolom
            ->logOnlyDirty() // Hanya rekam yang berubah
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Data Dokumen Arsip telah di-{$eventName}");
    }

    // ==========================================
    // RELASI DATABASE
    // ==========================================

    // Relasi: Dokumen ini milik Organisasi/Ranting mana?
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Relasi: Siapa Pengurus yang mengupload dokumen ini?
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
