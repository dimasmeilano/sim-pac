<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Inventaris extends Model
{
    use HasFactory, BelongsToOrganization, LogsActivity; // Jika trait ini ada, gunakan. Jika tidak, hapus saja "BelongsToOrganization"-nya.

    protected $table = 'inventaris';

    protected $guarded = [];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Pembuat Badge Kondisi Otomatis
    public function getKondisiBadgeAttribute()
    {
        if ($this->kondisi == 'baik') {
            return '<span class="badge badge-success px-2 py-1"><i class="fas fa-check-circle"></i> Baik</span>';
        } elseif ($this->kondisi == 'rusak_ringan') {
            return '<span class="badge badge-warning px-2 py-1"><i class="fas fa-tools"></i> Rusak Ringan</span>';
        } else {
            return '<span class="badge badge-danger px-2 py-1"><i class="fas fa-times-circle"></i> Rusak Berat</span>';
        }
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Rekam semua kolom
            ->logOnlyDirty() // Hanya rekam kolom yang nilainya berubah (saat diedit)
            ->dontSubmitEmptyLogs() // Jangan rekam kalau tidak ada perubahan
            ->setDescriptionForEvent(fn(string $eventName) => "Data Inventaris telah di-{$eventName}");
    }
}
