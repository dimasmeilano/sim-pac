<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkspaceFolder extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'izinkan_upload_publik' => 'boolean',
    ];

    // Folder ini milik Kegiatan apa?
    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    // Siapa yang buat folder ini?
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Daftar User yang dikasih kunci akses (jika folder private)
    public function authorizedUsers()
    {
        return $this->belongsToMany(User::class, 'folder_user');
    }

    // Isi file di dalam folder ini
    public function galeris()
    {
        return $this->hasMany(Galeri::class);
    }
}
