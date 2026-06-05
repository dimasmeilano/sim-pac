<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class MembersImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // PENTING: Mencegah error jika baris kosong ikut terbaca
        if (!isset($row['nama_lengkap']) || !isset($row['email'])) {
            return null;
        }

        // Cek apakah email sudah ada di database agar tidak error duplicate
        $existingUser = User::where('email', $row['email'])->first();
        if ($existingUser) {
            return null; // Lewati jika email sudah terdaftar
        }

        $user = User::create([
            'name'            => $row['nama_lengkap'],
            'email'           => $row['email'],
            'password'        => Hash::make('anggota123'), // Password default
            'nik'             => $row['nik'] ?? null,
            'tempat_lahir'    => $row['tempat_lahir'] ?? null,
            'tanggal_lahir'   => $this->transformDate($row['tanggal_lahir']),
            'jk'              => strtoupper($row['jenis_kelamin_l_p']) ?? null,
            'no_hp'           => $row['no_hp'] ?? null,
            'pendidikan'      => $row['pendidikan'] ?? null,
            // Otomatis mengunci organisasi sesuai dengan sekre yang sedang upload
            'organization_id' => auth()->user()->organization_id,
            'tgl_bergabung'   => now()->format('Y-m-d'),
            'status_anggota'  => 'aktif',
        ]);

        // Otomatis assign role anggota biasa
        $user->assignRole('anggota_biasa');

        return $user;
    }

    // Fungsi bantuan untuk membaca format tanggal Excel yang kadang aneh
    private function transformDate($value)
    {
        if (!$value) return null;
        try {
            return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value)->format('Y-m-d');
        } catch (\Exception $e) {
            return date('Y-m-d', strtotime($value));
        }
    }
}
