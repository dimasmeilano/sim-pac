<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakestaPeserta extends Model
{
    use HasFactory;

    // Mengizinkan semua kolom diisi kecuali ID
    protected $guarded = ['id'];

    // Relasi kembali ke Event Makesta
    public function event()
    {
        return $this->belongsTo(MakestaEvent::class, 'makesta_event_id');
    }
}
