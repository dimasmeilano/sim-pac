<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lpj extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Cast JSON agar array foto mudah dibaca
    protected $casts = [
        'foto_dokumentasi_terpilih' => 'array',
    ];

    public function programKerja()
    {
        return $this->belongsTo(ProgramKerja::class, 'program_kerja_id');
    }
}
