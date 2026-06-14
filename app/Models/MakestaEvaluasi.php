<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakestaEvaluasi extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    // Auto-convert text ke array
    protected $casts = [
        'data_evaluasi' => 'array',
    ];

    public function peserta()
    {
        return $this->belongsTo(MakestaPeserta::class, 'makesta_peserta_id');
    }
}
