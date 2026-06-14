<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakestaEvent extends Model
{
    use HasFactory;

    // Mengizinkan semua kolom diisi kecuali ID (Mencegah Mass Assignment Error)
    protected $guarded = ['id'];

    // Relasi ke tabel Organisasi (Siapa penyelenggaranya)
    public function organization()
    {
        return $this->belongsTo(\App\Models\Organization::class, 'organization_id');
    }

    // Relasi ke tabel Materi (1 Event punya Banyak Materi)
    public function materis()
    {
        return $this->hasMany(MakestaMateri::class, 'makesta_event_id');
    }

    // Relasi 1 Event punya Banyak Peserta
    public function pesertas()
    {
        return $this->hasMany(MakestaPeserta::class, 'makesta_event_id');
    }
}
