<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriArtikel extends Model
{
    use HasFactory;

    protected $table = 'kategori_artikels';
    protected $guarded = [];

    // Relasi: 1 Kategori punya BANYAK Artikel
    public function artikels()
    {
        return $this->hasMany(Artikel::class, 'kategori_id');
    }
}
