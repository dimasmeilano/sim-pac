<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\BelongsToOrganization;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Permission\Traits\HasRoles;

#[Fillable([
    'name',
    'email',
    'password',
    'organization_id',
    'nik',
    'tempat_lahir',
    'tanggal_lahir',
    'jk',
    'no_hp',
    'pendidikan',
    'foto',
    'qr_code',
    'tgl_bergabung',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles, BelongsToOrganization, LogsActivity;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }

    // Relasi ke organization
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    // Accessor untuk umur
    public function getUmurAttribute()
    {
        if ($this->tanggal_lahir) {
            return $this->tanggal_lahir->age;
        }
        return null;
    }

    // Accessor untuk jenis kelamin dalam teks
    public function getJkTextAttribute()
    {
        return $this->jk == 'L' ? 'Laki-laki' : 'Perempuan';
    }

    // Scope untuk anggota aktif
    public function scopeAktif($query)
    {
        return $query->where('status_anggota', 'aktif');
    }

    // Scope berdasarkan organization
    public function scopeByOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Cek apakah user adalah wakil sekretaris
     */
    public function isWakilSekretaris()
    {
        return $this->hasRole('wakil_sekretaris_pac') || $this->hasRole('wakil_sekretaris_ranting');
    }

    /**
     * Cek apakah user adalah wakil bendahara (PAC atau Ranting)
     */
    public function isWakilBendahara()
    {
        return $this->hasRole('wakil_bendahara_pac') || $this->hasRole('wakil_bendahara_ranting');
    }

    /**
     * Cek apakah user adalah wakil (sekretaris atau bendahara)
     */
    public function isWakil()
    {
        return $this->isWakilSekretaris() || $this->isWakilBendahara();
    }

    /**
     * Mendapatkan role wakil (untuk mencari pasangan)
     */
    public function getWakilRole()
    {
        if ($this->isWakilSekretaris()) {
            return 'wakil_sekretaris';
        }
        if ($this->isWakilBendahara()) {
            return 'wakil_bendahara';
        }
        return null;
    }

    public function getPartnerWakil()
    {
        // Ambil role user saat ini
        $roles = $this->getRoleNames();

        // Cari role yang mengandung 'wakil'
        $wakilRole = null;
        foreach ($roles as $role) {
            if (str_contains($role, 'wakil')) {
                $wakilRole = $role;
                break;
            }
        }

        if (!$wakilRole) {
            return null;
        }

        // Cari user lain dengan role yang SAMA, di organisasi yang SAMA, bukan diri sendiri
        $partner = User::role($wakilRole)
            ->where('organization_id', $this->organization_id)
            ->where('id', '!=', $this->id)
            ->first();

        // Jika tidak ada di organisasi yang sama, cari tanpa filter organisasi
        if (!$partner) {
            $partner = User::role($wakilRole)
                ->where('id', '!=', $this->id)
                ->first();
        }

        return $partner;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Rekam semua kolom
            ->logOnlyDirty() // Hanya rekam kolom yang nilainya berubah (saat diedit)
            ->dontSubmitEmptyLogs() // Jangan rekam kalau tidak ada perubahan
            ->setDescriptionForEvent(fn(string $eventName) => "Data User telah di-{$eventName}");
    }
}
