<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notulensi extends Model
{
    use HasFactory;

    // Pastikan nama tabelnya sesuai di database Anda
    protected $table = 'notulensi';

    protected $guarded = [];

    protected $casts = [
        'tanggal' => 'date',
    ];

    // Status Badge untuk mempermudah pemanggilan di Blade
    public function getStatusBadgeAttribute()
    {
        if ($this->status == 'final') {
            return '<span class="badge badge-success"><i class="fas fa-lock"></i> Final</span>';
        }
        return '<span class="badge badge-warning"><i class="fas fa-edit"></i> Draft</span>';
    }

    // ==========================================
    // RELASI DATABASE
    // ==========================================
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function notulis()
    {
        return $this->belongsTo(User::class, 'notulis_id');
    }
}
