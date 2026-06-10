<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Artikel extends Model
{
    use HasFactory;

    protected $table = 'artikels';
    protected $guarded = [];

    // Mengubah string tanggal di database menjadi objek Carbon (agar mudah diformat)
    protected $casts = [
        'published_at' => 'datetime',
    ];

    // Relasi: 1 Artikel dimiliki oleh 1 Kategori
    public function kategori()
    {
        return $this->belongsTo(KategoriArtikel::class, 'kategori_id');
    }

    // Relasi: 1 Artikel ditulis oleh 1 User (Admin/Pengurus)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function organization()
    {
        // Pastikan nama modelnya sesuai dengan yang Anda gunakan, misal: Organization::class
        return $this->belongsTo(Organization::class, 'organization_id');
    }

    /**
     * ACCESSOR: Membuat label Badge HTML otomatis berdasarkan status artikel
     * Cara memanggilnya di blade nanti: $artikel->status_badge
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft'           => '<span class="badge badge-secondary"><i class="fas fa-file"></i> Draft</span>',
            'menunggu_review' => '<span class="badge badge-warning"><i class="fas fa-hourglass-half"></i> Menunggu Review</span>',
            'revisi'          => '<span class="badge badge-danger"><i class="fas fa-edit"></i> Perlu Revisi</span>',
            'publish'         => '<span class="badge badge-success"><i class="fas fa-check-circle"></i> Terbit</span>',
        ];

        // Kembalikan badge sesuai status, jika tidak ada kembalikan badge abu-abu
        return $badges[$this->status] ?? '<span class="badge badge-secondary">Tidak Diketahui</span>';
    }
}
