<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class Organization extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'jenis_organisasi',
        'periode',
        'parent_id',
        'alamat',
        'kontak',
        'email',
        'website',
        'logo',
        'ketua_id',
        'wakil_ketua_1_id',
        'wakil_ketua_2_id',
        'wakil_ketua_3_id',
        'wakil_ketua_4_id',
        'wakil_ketua_5_id',
        'sekretaris_id',
        'wakil_sekretaris_1_id',
        'wakil_sekretaris_2_id',
        'wakil_sekretaris_3_id',
        'wakil_sekretaris_4_id',
        'wakil_sekretaris_5_id',
        'bendahara_id',
        'wakil_bendahara_1_id',
        'wakil_bendahara_2_id',
        'wakil_bendahara_3_id',
        'kop_surat_ipnu',
        'kop_surat_ippnu',
        'kop_surat_bersama',
        'ttd_ketua',
        'ttd_sekretaris',
    ];

    // Relasi
    public function ketua()
    {
        return $this->belongsTo(User::class, 'ketua_id');
    }

    public function bendahara()
    {
        return $this->belongsTo(User::class, 'bendahara_id');
    }

    public function sekretaris()
    {
        return $this->belongsTo(User::class, 'sekretaris_id');
    }

    // Relasi Wakil Ketua
    public function wakilKetua1()
    {
        return $this->belongsTo(User::class, 'wakil_ketua_1_id');
    }
    public function wakilKetua2()
    {
        return $this->belongsTo(User::class, 'wakil_ketua_2_id');
    }
    public function wakilKetua3()
    {
        return $this->belongsTo(User::class, 'wakil_ketua_3_id');
    }
    public function wakilKetua4()
    {
        return $this->belongsTo(User::class, 'wakil_ketua_4_id');
    }
    public function wakilKetua5()
    {
        return $this->belongsTo(User::class, 'wakil_ketua_5_id');
    }

    // Relasi Wakil Sekretaris
    public function wakilSekretaris1()
    {
        return $this->belongsTo(User::class, 'wakil_sekretaris_1_id');
    }
    public function wakilSekretaris2()
    {
        return $this->belongsTo(User::class, 'wakil_sekretaris_2_id');
    }
    public function wakilSekretaris3()
    {
        return $this->belongsTo(User::class, 'wakil_sekretaris_3_id');
    }
    public function wakilSekretaris4()
    {
        return $this->belongsTo(User::class, 'wakil_sekretaris_4_id');
    }
    public function wakilSekretaris5()
    {
        return $this->belongsTo(User::class, 'wakil_sekretaris_5_id');
    }

    // Relasi Wakil Bendahara
    public function wakilBendahara1()
    {
        return $this->belongsTo(User::class, 'wakil_bendahara_1_id');
    }
    public function wakilBendahara2()
    {
        return $this->belongsTo(User::class, 'wakil_bendahara_2_id');
    }
    public function wakilBendahara3()
    {
        return $this->belongsTo(User::class, 'wakil_bendahara_3_id');
    }

    public function parent()
    {
        return $this->belongsTo(Organization::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Organization::class, 'parent_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Model Events untuk sinkronisasi role
    protected static function booted()
    {
        // Event ketika bendahara_id berubah
        static::updating(function (Organization $organization) {
            if ($organization->isDirty('bendahara_id')) {
                static::syncBendaharaRole($organization);
            }

            if ($organization->isDirty('ketua_id')) {
                static::syncKetuaRole($organization);
            }

            if ($organization->isDirty('sekretaris_id')) {
                static::syncSekretarisRole($organization);
            }
        });
    }

    protected static function syncBendaharaRole(Organization $organization)
    {
        // Hapus role dari bendahara LAMA
        $originalId = $organization->getOriginal('bendahara_id');
        if ($originalId) {
            $oldUser = User::find($originalId);
            if ($oldUser) {
                $oldUser->removeRole('bendahara_pac');
                $oldUser->removeRole('bendahara_ranting');
            }
        }

        // Assign role ke bendahara BARU
        $newId = $organization->bendahara_id;
        if ($newId) {
            $newUser = User::find($newId);
            if ($newUser) {
                // Pilih role berdasarkan tipe organisasi
                if ($organization->type == 'pac') {
                    $newUser->assignRole('bendahara_pac');
                } else {
                    $newUser->assignRole('bendahara_ranting');
                }

                // Beri permission
                $newUser->givePermissionTo('view_keuangan');
            }
        }
    }

    protected static function syncKetuaRole(Organization $organization)
    {
        // Hapus role dari ketua LAMA
        $originalId = $organization->getOriginal('ketua_id');
        if ($originalId) {
            $oldUser = User::find($originalId);
            if ($oldUser) {
                $oldUser->removeRole('ketua_pac');
                $oldUser->removeRole('ketua_ranting');
            }
        }

        // Assign role ke ketua BARU
        $newId = $organization->ketua_id;
        if ($newId) {
            $newUser = User::find($newId);
            if ($newUser) {
                if ($organization->type == 'pac') {
                    $newUser->assignRole('ketua_pac');
                } else {
                    $newUser->assignRole('ketua_ranting');
                }
            }
        }
    }

    protected static function syncSekretarisRole(Organization $organization)
    {
        $originalId = $organization->getOriginal('sekretaris_id');
        if ($originalId) {
            $oldUser = User::find($originalId);
            if ($oldUser) {
                $oldUser->removeRole('sekretaris_pac');
                $oldUser->removeRole('sekretaris_ranting');
            }
        }

        $newId = $organization->sekretaris_id;
        if ($newId) {
            $newUser = User::find($newId);
            if ($newUser) {
                if ($organization->type == 'pac') {
                    $newUser->assignRole('sekretaris_pac');
                } else {
                    $newUser->assignRole('sekretaris_ranting');
                }
            }
        }
    }

    // Accessor
    public function getTypeTextAttribute()
    {
        $types = [
            'pac' => 'Pimpinan Anak Cabang',
            'ranting' => 'Pimpinan Ranting',
            'departemen' => 'Departemen',
            'lembaga' => 'Lembaga',
        ];
        return $types[$this->type] ?? ucfirst($this->type);
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

    public function getTingkatTextAttribute()
    {
        $map = [
            'pusat' => 'Pimpinan Pusat',
            'wilayah' => 'Pimpinan Wilayah',
            'cabang' => 'Pimpinan Cabang',
            'pac' => 'Pimpinan Anak Cabang',
            'ranting' => 'Pimpinan Ranting',
            'komisariat' => 'Pimpinan Komisariat',
        ];

        return $map[$this->type] ?? ucfirst($this->type);
    }
    /**
     * Mendapatkan nama lengkap organisasi untuk KOP SURAT (HURUF BESAR SEMUA)
     * Contoh: "PIMPINAN ANAK CABANG IKATAN PELAJAR NAHDLATUL ULAMA IPNU KEBOMAS"
     */
    public function getNamaOrganisasiKopAttribute()
    {
        $tingkat = strtoupper($this->tingkat_text); // PIMPINAN ANAK CABANG
        $organisasi = 'IKATAN PELAJAR NAHDLATUL ULAMA';
        $wilayah = strtoupper($this->nama_wilayah); // KEBOMAS

        return $tingkat . "\n" . $organisasi . "\n" . $wilayah;
    }

    public function getNamaOrganisasiLengkapAttribute()
    {
        $tingkat = ucwords(strtolower($this->tingkat_text)); // Pimpinan Anak Cabang
        $organisasi = 'Ikatan Pelajar Nahdlatul Ulama';
        $wilayah = ucwords(strtolower($this->nama_wilayah)); // Kebomas

        return trim("{$tingkat} {$organisasi}' {$wilayah}");
    }


    /**
     * Mendapatkan kalimat pembuka untuk surat
     */
    public function getPembukaSuratAttribute()
    {
        return $this->nama_organisasi_lengkap . ", setelah:";
    }

    /**
     * Mendapatkan nama wilayah dari nama organisasi
     * Contoh: "PAC IPNU Kebomas" -> "Kebomas"
     *         "PR IPPNU Desa Sukodadi" -> "Desa Sukodadi"
     */
    public function getNamaWilayahAttribute()
    {
        // Pisahkan nama organisasi
        $parts = explode(' ', $this->name);

        // Hapus kode organisasi (PAC, PR, IPNU, IPPNU)
        $exclude = ['PAC', 'PR', 'PC', 'PW', 'PP', 'IPNU', 'IPPNU', 'Bersama'];

        $filtered = array_filter($parts, function ($part) use ($exclude) {
            return !in_array(strtoupper($part), $exclude);
        });

        if (empty($filtered)) {
            return $this->name;
        }

        return implode(' ', $filtered);
    }

    /**
     * Mendapatkan format untuk kop surat
     */
    public function getKopOrganisasiAttribute()
    {
        $jenisTeks = '';
        if ($this->jenis_organisasi == 'ipnu') {
            $jenisTeks = 'IPNU';
        } elseif ($this->jenis_organisasi == 'ippnu') {
            $jenisTeks = 'IPPNU';
        } else {
            $jenisTeks = 'IPNU - IPPNU';
        }

        return implode("\n", array_filter([
            $this->tingkat_text,
            "IKATAN PELAJAR NAHDLATUL ULAMA",
            $jenisTeks,
            strtoupper($this->nama_wilayah)
        ]));
    }

    public function getTtdKetuaUrlAttribute()
    {
        return $this->ttd_ketua ? asset('storage/' . $this->ttd_ketua) : null;
    }

    // Accessor untuk TTD Sekretaris
    public function getTtdSekretarisUrlAttribute()
    {
        return $this->ttd_sekretaris ? asset('storage/' . $this->ttd_sekretaris) : null;
    }

    public function getStempelUrlAttribute()
    {
        return $this->stempel ? asset('storage/' . $this->stempel) : null;
    }
}
