<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notulensi extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'notulensi';

    protected $fillable = [
        'organization_id',
        'kegiatan_id',
        'agenda',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'pemimpin_rapat',
        'notulis_id',
        'pembahasan',
        'kesimpulan',
        'status'
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

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

    public function getStatusBadgeAttribute()
    {
        return $this->status == 'final'
            ? '<span class="badge badge-success"><i class="fas fa-check-double"></i> Final</span>'
            : '<span class="badge badge-secondary"><i class="fas fa-edit"></i> Draft</span>';
    }
}
