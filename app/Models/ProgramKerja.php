<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgramKerja extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'program_kerja';

    protected $fillable = [
        'organization_id',
        'nama',
        'deskripsi',
        'jenis',
        'tgl_mulai',
        'tgl_selesai',
        'status'
    ];

    protected $casts = [
        'tgl_mulai' => 'date',
        'tgl_selesai' => 'date',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function tugas()
    {
        return $this->hasMany(SubTugas::class, 'progja_id')->orderBy('urutan');
    }

    public function messages()
    {
        return $this->hasMany(Message::class, 'progja_id');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            'planning' => 'Perencanaan',
            'active' => 'Berjalan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];
        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    public function getJenisTextAttribute()
    {
        $jenis = [
            'ipnu' => 'IPNU',
            'ippnu' => 'IPPNU',
            'bersama' => 'Bersama',
        ];
        return $jenis[$this->jenis] ?? ucfirst($this->jenis);
    }

    public function repliedMessage()
    {
        return $this->belongsTo(Message::class, 'reply_to_id'); // Ganti Message::class dengan nama Model Anda jika berbeda
    }
}
