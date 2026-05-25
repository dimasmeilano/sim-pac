<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratTemplate extends Model
{
    use HasFactory;

    protected $table = 'surat_templates';

    protected $fillable = [
        'kode',
        'nama',
        'klasifikasi',
        'lampiran',
        'jenis_surat',
        'fields',
        'konten',
        'urutan',
        'is_active',
        'status'
    ];

    protected $casts = [
        'fields' => 'array',
    ];

    // Relasi ke surat keluar
    public function suratKeluar()
    {
        return $this->hasMany(SuratKeluar::class, 'template_id');
    }

    // Aksesor untuk fields (selalu array)
    public function getFieldsListAttribute()
    {
        return $this->fields ?? [];
    }

    // Aksesor untuk placeholder (ambil dari fields)
    public function getPlaceholderListAttribute()
    {
        return array_keys($this->fieldsList);
    }

    // Cek apakah ini surat khusus (tanpa perihal & tujuan)
    public function isSuratKhusus()
    {
        return in_array($this->jenis_surat, ['keputusan', 'pengesahan', 'tugas']);
    }
}
