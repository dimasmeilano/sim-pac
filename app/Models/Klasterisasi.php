<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\BelongsToOrganization;

class Klasterisasi extends Model
{
    use BelongsToOrganization;

    // Mengizinkan semua kolom diisi
    protected $guarded = [];

    // Mengonversi JSON menjadi Array secara otomatis
    protected $casts = [
        'p2_tabel_lembaga'   => 'array',
        'p2_tabel_pesantren' => 'array',
        'p3_tabel_mou'       => 'array',
        'p3_struktur_alumni' => 'array',
        'p3_kegiatan_alumni' => 'array',

        // Tambahan khusus IPPNU
        'p1_tabel_pimpinan'  => 'array',
        'p2_tabel_proker'    => 'array',
    ];

    // Relasi ke Organisasi
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Relasi untuk melihat siapa Sekretaris yang memeriksa
    public function sekretaris()
    {
        return $this->belongsTo(User::class, 'id_sekretaris')->withoutGlobalScopes();
    }

    // Relasi untuk melihat siapa Ketua yang mengesahkan
    public function ketua()
    {
        return $this->belongsTo(User::class, 'id_ketua')->withoutGlobalScopes();
    }
}
