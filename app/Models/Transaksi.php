<?php

namespace App\Models;

use App\Traits\BelongsToOrganization;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory, BelongsToOrganization;

    protected $table = 'transaksi';

    protected $fillable = [
        'organization_id',
        'jenis_organisasi',
        'program_kerja_id',
        'kegiatan_id',
        'kode_transaksi',
        'judul',
        'jenis',
        'nominal',
        'tanggal',
        'kategori',
        'keterangan',
        'bukti_file',
        'created_by',
        'status_validasi',
        'divalidasi_oleh',
        'catatan_validasi',
        'tanggal_validasi'
    ];



    protected $casts = [
        'tanggal' => 'date',
        'nominal' => 'decimal:2',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function programKerja()
    {
        return $this->belongsTo(ProgramKerja::class);
    }

    public function kegiatan()
    {
        return $this->belongsTo(Kegiatan::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getJenisTextAttribute()
    {
        return $this->jenis == 'masuk'
            ? '<span class="badge badge-success">Pemasukan</span>'
            : '<span class="badge badge-danger">Pengeluaran</span>';
    }

    public function getNominalFormatAttribute()
    {
        return 'Rp ' . number_format($this->nominal, 0, ',', '.');
    }

    public function getJenisOrganisasiTextAttribute()
    {
        $jenis = [
            'ipnu' => 'IPNU',
            'ippnu' => 'IPPNU',
            'bersama' => 'IPNU & IPPNU',
        ];
        return $jenis[$this->jenis_organisasi] ?? '-';
    }

    // Relasi ke validator (bendahara yang memvalidasi)
    public function validator()
    {
        return $this->belongsTo(User::class, 'divalidasi_oleh');
    }

    // Status validasi text dengan badge
    public function getStatusValidasiTextAttribute()
    {
        $statuses = [
            'draft' => '<span class="badge badge-secondary">Draft</span>',
            'menunggu' => '<span class="badge badge-warning">Menunggu Validasi</span>',
            'disetujui' => '<span class="badge badge-success">Disetujui</span>',
            'ditolak' => '<span class="badge badge-danger">Ditolak</span>',
        ];
        return $statuses[$this->status_validasi] ?? '-';
    }
}
