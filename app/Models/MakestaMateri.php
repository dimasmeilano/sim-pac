<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MakestaMateri extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function nilais()
    {
        return $this->hasMany(MakestaNilai::class, 'makesta_materi_id');
    }
    // Relasi kembali ke Event utamanya
    public function event()
    {
        return $this->belongsTo(MakestaEvent::class, 'makesta_event_id');
    }
}
