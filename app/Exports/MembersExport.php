<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembersExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $user = auth()->user();

        if ($user->hasRole('super_admin') || $user->hasRole('sekretaris_pac')) {
            return User::whereNotNull('organization_id')->with('organization')->get();
        }

        return User::where('organization_id', $user->organization_id)->with('organization')->get();
    }

    // ... (Fungsi headings(), map(), dan styles() biarkan seperti sebelumnya) ...

    public function headings(): array
    {
        return [
            'NO',
            'NAMA LENGKAP',
            'EMAIL',
            'ASAL ORGANISASI',
            'NIK',
            'NO. HP / WA',
            'TEMPAT LAHIR',
            'TANGGAL LAHIR',
            'JENIS KELAMIN',
            'PENDIDIKAN',
            'STATUS',
            'TANGGAL BERGABUNG'
        ];
    }

    public function map($member): array
    {
        // INI KUNCI NOMOR URUTNYA (Akan selalu mulai dari 1)
        static $rowNumber = 0;
        $rowNumber++;

        return [
            $rowNumber,
            $member->name,
            $member->email,
            $member->organization ? $member->organization->name : '-',
            $member->nik ? "'" . $member->nik : '-',
            $member->no_hp ? "'" . $member->no_hp : '-',
            $member->tempat_lahir ?? '-',
            $member->tanggal_lahir ? date('d/m/Y', strtotime($member->tanggal_lahir)) : '-',
            $member->jk == 'L' ? 'Laki-laki' : ($member->jk == 'P' ? 'Perempuan' : '-'),
            $member->pendidikan ?? '-',
            ucfirst($member->status_anggota),
            $member->tgl_bergabung ? date('d/m/Y', strtotime($member->tgl_bergabung)) : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']], 'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF28A745']]],
        ];
    }
}
