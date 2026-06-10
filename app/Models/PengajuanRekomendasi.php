<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class PengajuanRekomendasi extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'pengajuan_rekomendasis';

    protected $guarded = [];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Data Pengajuan Rekomendasi telah di-{$eventName}");
    }
}
